<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:49
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\InfoColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;

class ParserRecordList extends Parser
{
	/**
	 * @var null
	 */
	protected $m_clParserXSD=null;

	/**
	 * @var array
	 * map qui associe une référence au data qui correspond
	 */
	public $m_MapRef2Data;

	/**
	 * @var array
	 * map qui contient l'association IDTableau=>IDEnreg=>Objet Record
	 */
	public $m_MapIDTableauIDEnreg2Record;

	/**
	 * @var array;
	 * tableau qui contient l'ordre des enregistrements avec conservation de l'ordre de la réponse
	 */
	public $m_TabEnregTableau;


	/**
	 * @var \SimpleXMLElement
	 */
	protected $m_clXML;

	/**
	 * @param Form $clForm
	 * @param Element $clElement
	 * @return null|Record
	 */
	public function clGetRecord(Form $clForm, Element $clElement)
	{
		return $this->clGetRecordFromId($clForm->getID(), $clElement->getID());
	}

	/**
	 * @param $sIDForm
	 * @param $sIDEreng
	 * @return null|Record
	 */
	public function clGetRecordFromId($sIDForm, $sIDEnreg)
	{
		if (!isset($this->m_MapIDTableauIDEnreg2Record)
			||  !isset($this->m_MapIDTableauIDEnreg2Record[$sIDForm])
			||  !isset($this->m_MapIDTableauIDEnreg2Record[$sIDForm][$sIDEnreg]))
		{
			return null;
		}

		return $this->m_MapIDTableauIDEnreg2Record[$sIDForm][$sIDEnreg];
	}

	/**
	 * @param Record $clRecord
	 */
	protected function _SetRecord(Record $clRecord)
	{
		$sIDForm=$clRecord->getIDTableau();
		if (!isset($this->m_MapIDTableauIDEnreg2Record[$sIDForm]))
		{
			$this->m_MapIDTableauIDEnreg2Record[$sIDForm]=array();
		}

		$this->m_MapIDTableauIDEnreg2Record[$sIDForm][$clRecord->getIDEnreg()]=$clRecord;
		return $this;
	}

	/**
	 * @param $form
	 * @return array : tableau des identifiants des enregistrements
	 */
	public function GetTabIDEnregFromForm($form)
	{
		if ($form instanceof Form)
		{
			$form = $form->getID();
		}

		if (!isset($this->m_MapIDTableauIDEnreg2Record)
			||  !isset($this->m_MapIDTableauIDEnreg2Record[$form]))
		{
			return array();
		}

		return array_keys($this->m_MapIDTableauIDEnreg2Record[$form]);
	}


	/**
	 * @param $form
	 * @return array|EnregTableauArray : tableau des identifiants des enregistrements
	 */
	public function GetTabEnregTableau()
	{
		return $this->m_TabEnregTableau;
	}

	/**
	 * @param $nRef
	 * @return Data
	 */
	public function clGetData($nRef)
	{
		return $this->m_MapRef2Data[$nRef];
	}

	/**
	 * @return array
	 */
	public function GetTabData()
	{
		return $this->m_MapRef2Data;
	}

	/**
	 * @param \SimpleXMLElement $ndSchema
	 * @param                   $nNiv
	 */
	public function ParseXSD(\SimpleXMLElement $ndSchema, $nNiv)
	{
		$this->m_clParserXSD = new ParserXSDSchema();
		$this->m_clParserXSD->Parse($ndSchema, $nNiv);
	}

	/**
	 * @param string $sTagName
	 */
	protected function _sGetIDFromTagName($sTagName)
	{
		if (strncmp($sTagName, 'id_', strlen('id_')) != 0)
		{
			return '';
		}

		return str_replace('id_', '', $sTagName);
	}

	/**
	 * Parse la balise Data
	 * @param \SimpleXMLElement $clXML
	 */
	protected function _ParseData(\SimpleXMLElement $ndData)
	{
		/*
		 * <Data simax:size="12770"
		 *  simax:filename="C:\Users\NINON~1.NOU\AppData\Local\Temp\Utilisateur - superviseur.html"
		 * simax:typemime="text/html"
		 * simax:encoding="base64"
		 * simax:ref="0"> ... </Data>
		 */

		$TabAttrib = $ndData->attributes(self::NAMESPACE_NOUT_XML);

		$clData              = new Data();
		$clData->m_nRef      = (int) $TabAttrib['ref'];
		$clData->m_nSize     = (int) $TabAttrib['size'];
		$clData->m_sEncoding = (string) $TabAttrib['encoding'];
		$clData->m_sFileName = (string) $TabAttrib['filename'];
		$clData->m_sMimeType = (string) $TabAttrib['typemime'];
		$clData->m_sContent  = (string) $ndData;

		$this->m_MapRef2Data[$clData->m_nRef] = $clData;
	}

	/**
	 * @param \SimpleXMLElement $ndXML
	 * @param                   $sIDForm
	 * @param                   $nNiv
	 */
	public function ParseXML(\SimpleXMLElement $ndXML, $sIDForm, $nNiv)
	{
		$this->m_clXML = $ndXML;

		//on commence par parser les balises data s'il y en a
		$this->m_MapRef2Data = array();

		$this->m_clXML->registerXPathNamespace('n', self::NAMESPACE_NOUT_XML);
		$aData = $this->m_clXML->xpath('/xml/n:Data');
		if (!empty($aData))
		{
			foreach($aData as $ndData)
			{
				$this->_ParseData($ndData);
			}
		}


		$this->m_MapIDTableauIDEnreg2Record       = array();
		$this->m_TabEnregTableau                  = new EnregTableauArray();

		$aRecords = $this->m_clXML->xpath('/xml/id_'.$sIDForm);
		if (!empty($aRecords))
		{
			foreach ($aRecords as $clNoeud)
			{
				$clRecord = $this->__clParseRecord($clNoeud);
				if (!is_null($clRecord))
				{
					$this->m_TabEnregTableau->Add($clRecord->getIDTableau(), $clRecord->getIDEnreg());
				}
			}
		}
	}

	/**
	 * @param string $sIDForm
	 * @param string $sIDEnreg
	 * @return Record
	 */
	protected function _clParseRecord($sIDForm, $sIDEnreg)
	{
		$aRecords = $this->m_clXML->xpath('/xml/id_'.$sIDForm.'[@n:id="'.$sIDEnreg.'"]');
		if (!empty($aRecords))
		{
			return $this->__clParseRecord($aRecords[0]);
		}
		return null;
	}


	/**
	 * Parse un élément XML
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 * @return Record
	 */
	protected function __clParseRecord(\SimpleXMLElement $clXML)
	{
		//<id_47909919412330 simax:id="33475861129246" simax:title="Janvier">

		$TabAttrib = $clXML->attributes(self::NAMESPACE_NOUT_XML);

		$sIDTableau = str_replace('id_', '', $clXML->getName());
		$sIDEnreg   = (string) $TabAttrib['id'];

		$clStructureElement = $this->clGetStructureElement($sIDTableau);
		$clRecord = new Record($sIDTableau, $sIDEnreg, (string) $TabAttrib['title'], $clStructureElement);

		$this->_SetRecord($clRecord)
			 ->_ParseColumns($clRecord, $clStructureElement, $clXML, $sIDTableau, $sIDEnreg);

		return $clRecord;
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function _ParseColumns(Record $clRecord, $clStructureElement,\SimpleXMLElement $clXML)
	{
		foreach ($clXML->children() as $ndColonne)
		{
			if (!is_null($clStructureElement))
			{
				$this->__ParseColumn($clRecord, $clStructureElement, $ndColonne);
				continue;
			}

			$this->__ParseColumnWithoutStruct($clRecord, $ndColonne);
		}
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function __ParseColumn(Record $clRecord, StructureElement $clStructureElement,\SimpleXMLElement $ndColonne)
	{
		$sNom            = $ndColonne->getName();
		$TabAttribNOUT   = $ndColonne->attributes(self::NAMESPACE_NOUT_XML);
		$TabAttribLayout = $ndColonne->attributes(self::NAMESPACE_NOUT_LAYOUT);

		$sIDColonne = str_replace('id_', '', $sNom);
		$clInfoColonne = new InfoColonne($sIDColonne, $TabAttribNOUT, $TabAttribLayout);

		$clStructureColonne = $clStructureElement->getStructureColonne($sIDColonne);
		$eTypeElement = $clStructureColonne->getTypeElement();
		switch($eTypeElement)
		{
			case StructureColonne::TM_Separateur:
				$this->_ParseColumns($clRecord, $clStructureElement, $ndColonne);
				break;

			case StructureColonne::TM_ListeElem:
			{
				$sIDFormLie = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);

				$Valeur = array();
				$aRecordLie = array();
				if (!empty($ndColonne->children()))
				{
					foreach ($ndColonne->children() as $ndValeur)
					{
						$sIDEnreg = (string) $ndValeur;
						$ValeurColonne[] = $sIDEnreg;

						$clRecordLie = $this->clGetRecordFromId($sIDFormLie, $Valeur);
						if (is_null($clRecordLie))
						{
							$clRecordLie = $this->_clParseRecord($sIDFormLie, $Valeur);
						}
						if (!is_null($clRecordLie))
						{
							$aRecordLie[]=$clRecordLie;
						}
					}
				}
				$clRecord->setValCol($clInfoColonne->getIDColonne(), $Valeur, false); //false car pas modifier par l'utilisateur ici
				break;
			}

			case StructureColonne::TM_Tableau:
			{
				$Valeur = (string) $ndColonne;
				$sIDFormLie = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);
				$clRecordLie = $this->clGetRecordFromId($sIDFormLie, $Valeur);
				if (is_null($clRecordLie))
				{
					$clRecordLie = $this->_clParseRecord($sIDFormLie, $Valeur);
				}

				if (!is_null($clRecordLie))
				{
					$clRecord->addRecordLie($clRecordLie);
				}

				$clRecord->setValCol($clInfoColonne->getIDColonne(), $Valeur, false); //false car pas modifier par l'utilisateur ici

				break;
			}

			default:
			{
				$Valeur = (string) $ndColonne;
				$clRecord->setValCol($clInfoColonne->getIDColonne(), $Valeur, false); //false car pas modifier par l'utilisateur ici
				break;
			}
		}
	}


	/**
	 * @param Record            $clRecord
	 * @param \SimpleXMLElement $ndColonne
	 */
	protected function __ParseColumnWithoutStruct(Record $clRecord, \SimpleXMLElement $ndColonne)
	{
		throw new \Exception('xml sans structure non géré');

//		$sNom            = $ndColonne->getName();
//		$TabAttribNOUT   = $ndColonne->attributes(self::NAMESPACE_NOUT_XML);
//		$TabAttribLayout = $ndColonne->attributes(self::NAMESPACE_NOUT_LAYOUT);
//
//		$clInfoColonne = new InfoColonne(str_replace('id_', '', $sNom), $TabAttribNOUT, $TabAttribLayout);
//
//		$ValeurColonne = null;
//		if ($ndColonne->count()>0)
//		{
//			//on a des fils
//			//on a pas la structure
//			//on regarde si le fils est dans le tableau d'element premier niveau $TabFormPresent
//			$bFormulaire = false;
//			foreach ($ndColonne->children() as $ndFils)
//			{
//				$sID = str_replace('id_', '', $ndFils->getName());
//				if (array_search($sID, $TabFormPresent) == false)
//				{
//					$bFormulaire = true;
//				}
//
//				break;
//			}
//
//			if ($bFormulaire)
//			{
//				//c'est pas un separateur mais une colonne liste
//				$ValeurColonne = array();
//				foreach ($ndColonne->children() as $ndValeur)
//				{
//					$ValeurColonne[] = (string) $ndValeur;
//				}
//			}
//			else
//			{
//				//c'est un séparateur, il faut faire les colonnes filles
//				$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
//			}
//
//		}
//		else
//		{
//			$ValeurColonne = (string) $ndColonne;
//		}
//
//		$this->m_MapIDTableauIDEnreg2Record[$sIDTableau][$sIDEnreg]
//			->setInfoColonne($clInfoColonne)
//			->setValCol($clInfoColonne->getIDColonne(), $ValeurColonne, false); //false car on parse le xml, donc par modifié utilisateur
	}





}