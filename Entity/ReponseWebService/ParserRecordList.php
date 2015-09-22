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
	 * @var ParserXSDSchema
	 */
	protected $m_clParserXSD=null;

	/**
	 * @var array
	 * map qui associe une référence au data qui correspond
	 */
	public $m_MapRef2Data;

	/**
	 * @var array;
	 * tableau qui contient l'ordre des enregistrements avec conservation de l'ordre de la réponse
	 */
	public $m_TabEnregTableau;

	/**
	 * @var RecordCache
	 */
	protected $m_clRecordCache;

	/**
	 * @var \SimpleXMLElement
	 */
	protected $m_clXML;


	public function __construct()
	{
		$this->m_clRecordCache = new RecordCache();
		$this->m_TabEnregTableau = new EnregTableauArray();
	}

	/**
	 * @param XMLResponseWS $clResponseXML
	 * @return null|Record
	 */
	public function getRecord(XMLResponseWS $clResponseXML)
	{
		return $this->m_clRecordCache->getRecord($clResponseXML->clGetForm()->getID(), $clResponseXML->clGetElement()->getID());
	}

	/**
	 * @return RecordCache
	 */
	public function getFullCache()
	{
		return $this->m_clRecordCache;
	}

    /**
     * @param $sIDForm
     * @param $sIDEreng
     * @return null|Record
     */
    public function getRecordFromID($sIDForm, $sIDEnreg)
    {
		if(!is_null($this->m_clRecordCache))
		{
			return $this->m_clRecordCache->getRecord($sIDForm, $sIDEnreg);
		}

    }

	/**
	 * @param $sIDForm
	 * @param $nNiv
	 * @return null|StructureElement
	 */
    public function getStructureElem($sIDForm, $nNiv)
    {
        if (!is_null($this->m_clParserXSD))
        {
			return $this->m_clParserXSD->clGetStructureElement($sIDForm, $nNiv);
        }
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
	 * @param \SimpleXMLElement $ndSchema
	 * @param                   $nNiv
	 */
	public function ParseXSD(\SimpleXMLElement $ndSchema, $nNiv)
	{
		$this->m_clParserXSD = new ParserXSDSchema();
		$this->m_clParserXSD->Parse($nNiv, $ndSchema);
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
		$this->m_clXML = new \SimpleXMLElement($ndXML->asXML());


		//on commence par parser les balises data s'il y en a
		$this->m_MapRef2Data = array();


		$this->m_clXML->registerXPathNamespace('n', self::NAMESPACE_NOUT_XML);
		$aData = $this->m_clXML->xpath('/xml/n:Data');
		if (is_array($aData) && !empty($aData))
		{
			foreach($aData as $ndData)
			{
				$this->_ParseData($ndData);
			}
		}

		$aRecords = $this->m_clXML->xpath("/xml/id_{$sIDForm}[@n:xsdLevel=\"{$nNiv}\"]");
		if (is_array($aRecords) && !empty($aRecords))
		{
			foreach ($aRecords as $clNoeud)
			{
				$clRecord = $this->__clParseRecord($nNiv, $clNoeud);
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
	protected function _clParseRecord($nNiv, $sIDForm, $sIDEnreg)
	{
		$aRecords = $this->m_clXML->xpath("/xml/id_{$sIDForm}[@n:id=\"{$sIDEnreg}\"][@n:xsdLevel=\"{$nNiv}\"]");
		if (is_array($aRecords) && !empty($aRecords))
		{
			return $this->__clParseRecord($nNiv, $aRecords[0]);
		}
		return null;
	}


	/**
	 * Parse un élément XML
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 * @return Record
	 */
	protected function __clParseRecord($nNiv, \SimpleXMLElement $clXML)
	{
		//<id_47909919412330 simax:id="33475861129246" simax:title="Janvier">

		$TabAttrib = $clXML->attributes(self::NAMESPACE_NOUT_XML);

		$sIDTableau = str_replace('id_', '', $clXML->getName());
		$sIDEnreg   = (string) $TabAttrib['id'];

		$clStructureElement = $this->m_clParserXSD->clGetStructureElement($sIDTableau);
		$clRecord = new Record($sIDTableau, $sIDEnreg, (string) $TabAttrib['title'], $nNiv, $clStructureElement);

		$this->m_clRecordCache->SetRecord($nNiv, $clRecord);
		$this->_ParseColumns($nNiv, $clRecord, $clStructureElement, $clXML, $sIDTableau, $sIDEnreg);

		return $clRecord;
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function _ParseColumns($nNiv, Record $clRecord, $clStructureElement,\SimpleXMLElement $clXML)
	{
		if (count($clXML->children())>0)
		{
			foreach ($clXML->children() as $ndColonne)
			{
				if (!is_null($clStructureElement))
				{
					$this->__ParseColumn($nNiv, $clRecord, $clStructureElement, $ndColonne);
					continue;
				}

				$this->__ParseColumnWithoutStruct($nNiv, $clRecord, $ndColonne);
			}
		}
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function __ParseColumn($nNiv, Record $clRecord, StructureElement $clStructureElement,\SimpleXMLElement $ndColonne)
	{
		$sNom            = $ndColonne->getName();
		$TabAttribNOUT   = $ndColonne->attributes(self::NAMESPACE_NOUT_XML);
		$TabAttribLayout = $ndColonne->attributes(self::NAMESPACE_NOUT_LAYOUT);

		$sIDColonne = str_replace('id_', '', $sNom);
		$clInfoColonne = new InfoColonne($sIDColonne, $TabAttribNOUT, $TabAttribLayout);
        $clRecord->setInfoColonne($clInfoColonne);

		$clStructureColonne = $clStructureElement->getStructureColonne($sIDColonne);
		//la colonne n'est pas forcément décrite dans le xsd ???
		if (!is_null($clStructureColonne))
		{
			$eTypeElement = $clStructureColonne->getTypeElement();
		}
		else
		{
			$eTypeElement = '';
		}

		switch($eTypeElement)
		{
			case StructureColonne::TM_Separateur:
				$this->_ParseColumns($nNiv, $clRecord, $clStructureElement, $ndColonne);
				break;

			case StructureColonne::TM_ListeElem:
			{
				$sIDFormLie = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);

				$Valeur = array();
				$aRecordLie = array();

				//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
				if (count($ndColonne->children())>0)
				{
					foreach ($ndColonne->children() as $ndValeur)
					{
						$sIDEnregLie = (string) $ndValeur;
						$Valeur[] = $sIDEnregLie;

						$clRecordLie = $this->m_clRecordCache->getRecordFromIdLevel($sIDFormLie, $sIDEnregLie, $nNiv+1);
						if (is_null($clRecordLie))
						{
							$clRecordLie = $this->_clParseRecord($nNiv+1, $sIDFormLie, $sIDEnregLie);
						}
						if (!is_null($clRecordLie))
						{
							$aRecordLie[]=$clRecordLie;
						}
					}
				}
				if (!empty($aRecordLie))
				{
					$clRecord->addTabRecordLie($aRecordLie);
				}
				$clRecord->setValCol($clInfoColonne->getIDColonne(), $Valeur, false); //false car pas modifier par l'utilisateur ici
				break;
			}

			case StructureColonne::TM_Tableau:
			{
				$Valeur = (string) $ndColonne;
				$sIDFormLie = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);
				$clRecordLie = $this->m_clRecordCache->getRecordFromIdLevel($sIDFormLie, $Valeur, StructureElement::NV_XSD_LienElement);
				if (is_null($clRecordLie))
				{
					$clRecordLie = $this->_clParseRecord(StructureElement::NV_XSD_LienElement, $sIDFormLie, $Valeur);
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
                $ref = (string)$TabAttribNOUT['ref'];
                if (!empty($ref) && isset($this->m_MapRef2Data[$ref]))
                {
                    $Valeur = $this->m_MapRef2Data[$ref];
                }
                else
                {
                    $Valeur = (string) $ndColonne;
                }

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