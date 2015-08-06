<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:49
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


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
	public $m_MapIDTableau2IDEnreg2Record;

	/**
	 * @var array;
	 * tableau qui contient l'ordre des enregistrements avec conservation de l'ordre de la réponse
	 */
	public $m_TabEnregTableau;

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
	public function clGetRecordFromId($sIDForm, $sIDEreng)
	{
		if (!isset($this->m_MapIDTableau2IDEnreg2Record)
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$sIDForm])
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$sIDForm][$sIDEreng]))
		{
			return;
		}

		return $this->m_MapIDTableau2IDEnreg2Record[$sIDForm][$sIDEreng];
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

		if (!isset($this->m_MapIDTableau2IDEnreg2Record)
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$form]))
		{
			return array();
		}

		return array_keys($this->m_MapIDTableau2IDEnreg2Record[$form]);
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
	 * @param \SimpleXMLElement $ndXML
	 * @param                   $nNiv
	 */
	public function ParseXML(\SimpleXMLElement $ndXML, $sIDForm, $nNiv)
	{
		$this->m_MapRef2Data = array();

		$this->m_MapIDTableau2IDEnreg2Record       = array();
		$this->m_TabEnregTableau                   = new EnregTableauArray();

		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
		 * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		//on commence par faire un premier tour, pour récuperer les formulaires présent au premier niveau
		$TabFormPresent = array();
		foreach ($ndXML->children() as $clNoeud)
		{
			$sTagName = $clNoeud->getName();
			if (strncmp($sTagName, 'id_', strlen('id_')) == 0)
			{
				$TabFormPresent[] = str_replace('id_', '', $sTagName);
			}
		}

		foreach ($ndXML->children() as $clNoeud)
		{
			$sTagName = $clNoeud->getName();
			if (strncmp($sTagName, 'id_', strlen('id_')) == 0)
			{
				$this->__ParseRecord($clNoeud, $TabFormPresent, $sIDForm);
				continue;
			}

			if (strcmp($sTagName, 'Data') == 0)
			{
				$this->_ParseData($clNoeud);
				continue;
			}
		}
	}


	/**
	 * Parse un élément XML
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 */
	protected function __ParseRecord(\SimpleXMLElement $clXML, $TabFormPresent, $sIDForm)
	{
		//<id_47909919412330 simax:id="33475861129246" simax:title="Janvier">

		$TabAttrib = $clXML->attributes(self::NAMESPACE_NOUT_XML);

		$sIDTableau = str_replace('id_', '', $clXML->getName());
		$sIDEnreg   = (string) $TabAttrib['id'];

		if (isset($sIDForm) && ($sIDForm == $sIDTableau))
		{
			$this->m_TabEnregTableau->AddNouveau($sIDTableau, $sIDEnreg);
		}

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau]))
		{
			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau] = array();
		}

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]))
		{
			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg] = new Record($sIDTableau, $sIDEnreg, (string) $TabAttrib['title'], $this->clGetStructureElement($sIDTableau));
		}

		$this->__ParseColumnRecord($clXML, $TabFormPresent, $sIDTableau, $sIDEnreg);
	}

	/**
	 * Parse la colonne d'un enregistrement
	 * @param \SimpleXMLElement $clXML
	 * @param $TabFormPresent
	 * @param $sIDTableau
	 * @param $sIDEnreg
	 */
	protected function __ParseColumnRecord(\SimpleXMLElement $clXML, $TabFormPresent, $sIDTableau, $sIDEnreg)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		foreach ($clXML->children() as $ndColonne)
		{
			$sNom            = $ndColonne->getName();
			$TabAttribNOUT   = $clXML->attributes(self::NAMESPACE_NOUT_XML);
			$TabAttribLayout = $clXML->attributes(self::NAMESPACE_NOUT_LAYOUT);

			$clInfoColonne = new InfoColonne(str_replace('id_', '', $sNom), $TabAttribNOUT, $TabAttribLayout);

			$ValeurColonne = null;
			if ($ndColonne->count()>0)
			{
				//on a des fils
				$clStructElem = $this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->clGetStructElem();
				if (isset($clStructElem))
				{
					$sTypeElement = $clStructElem->getTypeElement($clInfoColonne->getIDColonne());
					//on a la structure de l'enregistrement
					if ($sTypeElement == StructureColonne::TM_Separateur)
					{
						//c'est un séparateur, il faut faire les colonnes filles
						$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
					}
					else
					{
						//c'est pas un séparateur, cela devrait normalement être un TM_ListeElem
						if ($sTypeElement == StructureColonne::TM_ListeElem)
						{
							//il faut prendre la valeur des colonnes filles et mettre dans un tableau
							$ValeurColonne = array();
							foreach ($ndColonne->children() as $ndValeur)
							{
								$ValeurColonne[] = (string) $ndValeur;
							}
						}
					}
				}
				else
				{
					//on a pas la structure
					//on regarde si le fils est dans le tableau d'element premier niveau $TabFormPresent
					$bFormulaire = false;
					foreach ($ndColonne->children() as $ndFils)
					{
						$sID = str_replace('id_', '', $ndFils->getName());
						if (array_search($sID, $TabFormPresent) == false)
						{
							$bFormulaire = true;
						}

						break;
					}

					if ($bFormulaire)
					{
						//c'est pas un separateur mais une colonne liste
						$ValeurColonne = array();
						foreach ($ndColonne->children() as $ndValeur)
						{
							$ValeurColonne[] = (string) $ndValeur;
						}
					}
					else
					{
						//c'est un séparateur, il faut faire les colonnes filles
						$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
					}
				}
			}
			else
			{
				$ValeurColonne = (string) $ndColonne;
			}

			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]
				->setInfoColonne($clInfoColonne)
				->setValCol($clInfoColonne->getIDColonne(), $ValeurColonne, false); //false car on parse le xml, donc par modifié utilisateur
		}
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

		$TabAttrib = $ndData->attributes('http://www.nout.fr/soap');

		$clData              = new Data();
		$clData->m_nRef      = (int) $TabAttrib['ref'];
		$clData->m_nSize     = (int) $TabAttrib['size'];
		$clData->m_sEncoding = (string) $TabAttrib['encoding'];
		$clData->m_sFileName = (string) $TabAttrib['filename'];
		$clData->m_sMimeType = (string) $TabAttrib['typemime'];
		$clData->m_sContent  = (string) $ndData;

		$this->m_MapRef2Data[$clData->m_nRef] = $clData;
	}




}