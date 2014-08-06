<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 10:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Element;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Form;

/**
 * Class RecordManager
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class RecordParser
{
	public $m_MapIDTableau2Niv2StructureElement;
	public $m_MapIDTableau2IDEnreg2Record;

	public function __construct()
	{
		$this->m_MapIDTableau2Niv2StructureElement = array();
		$this->m_MapIDTableau2IDEnreg2Record = array();
	}

	public function clGetStructureElement($sIDTableau)
	{
		if (!isset($this->m_MapIDTableau2Niv2StructureElement) ||
			!isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau]))
			return null;

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_Enreg]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_Enreg];

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_SousEnreg]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_SousEnreg];

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_EnregSansEntete]))
			return $this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][StructureElement::NV_XSD_EnregSansEntete];

		return null;
	}

	public function clGetRecord(Form $clForm, Element $clElement)
	{
		if (    !isset($this->m_MapIDTableau2IDEnreg2Record)
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$clForm->getID()])
			||  !isset($this->m_MapIDTableau2IDEnreg2Record[$clForm->getID()][$clElement->getID()]))
			return null;

		return $this->m_MapIDTableau2IDEnreg2Record[$clForm->getID()][$clElement->getID()];
	}


	/**
	 * @param $nNiveau
	 * @param \SimpleXMLElement $clSchema
	 * @param array $MapStructureElement
	 * @return array
	 */
	protected function _ParseXSD($nNiveau, \SimpleXMLElement $clSchema)
	{
		//récupération du noeud element fils de schema
		$ndElement = $clSchema->children('http://www.w3.org/2001/XMLSchema')->element;

		$TabAttribXS = $ndElement->attributes('http://www.w3.org/2001/XMLSchema');
		$TabAttribSIMAX = $ndElement->attributes('http://www.nout.fr/XMLSchema');

		//on récupère l'id du formulaire, son libelle et le type
		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);

		if (isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau]) && isset($this->m_MapIDTableau2Niv2StructureElement[$sIDTableau][$nNiveau]))
			return ; //on a déjà parsé cette partie de l'XSD

		$clStructureElement = new StructureElement();
		$clStructureElement->m_nNiveau = $nNiveau;
		$clStructureElement->m_nID = $sIDTableau;
		$clStructureElement->m_sLibelle = (string)$TabAttribSIMAX['name'];

		//$sType = $TabAttribSIMAX['tableType'];

		$ndSequence = $ndElement->children('http://www.w3.org/2001/XMLSchema')->complexType
								->children('http://www.w3.org/2001/XMLSchema')->sequence;

		$this->__ParseXSDSequence($clStructureElement, $ndSequence, null);
	}

	/**
	 * @param $clStructCurrent (StructureElement ou StructureColonne)
	 * @param $MapStructureElement
	 * @param \SimpleXMLElement $clSequence
	 * @return mixed
	 */
	protected function __ParseXSDSequence( StructureElement $clStructElement, \SimpleXMLElement $clSequence, $sIDColonnePere)
	{
		//'http://www.nout.fr/XMLSchema'

		foreach($clSequence->children('http://www.w3.org/2001/XMLSchema') as $ndNoeud)
		{
			if ($ndNoeud->getName() != 'element')
				continue; //je cherche quel les xs:element

			$clAttribXS = $ndNoeud->attributes('http://www.w3.org/2001/XMLSchema');
			$clAttribNOUT = $ndNoeud->attributes('http://www.nout.fr/XMLSchema');

			$sIDColonne = str_replace('id_', '', (string)$clAttribXS->name);
			$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]=new StructureColonne($sIDColonne, $clAttribNOUT);

			switch($clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_eTypeElement)
			{
				case StructureColonne::TM_ListeElem:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
												->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$this->_ParseXSD(StructureElement::NV_XSD_SousEnreg, $ndSequence);
					}
					break;
				case StructureColonne::TM_Separateur:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
							->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$this->__ParseXSDSequence($clStructElement, $ndSequence, $sIDColonne);
					}
					break;
				case '':
					//il faut vérifier si pas extension d'un type de base (texte avec une limite de 100)
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSimpleType = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->simpleType
							->children('http://www.w3.org/2001/XMLSchema')->restriction;

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_eTypeElement = (string)$ndSimpleType->attributes('http://www.w3.org/2001/XMLSchema')['base'];

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction = new ColonneRestriction();

						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_MAXLENGTH:
									$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_ValeurRestriction = (int)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
									break;
							}
						}

					}
					break;

				case StructureColonne::TM_Combo:
					//il faut vérifier si pas extension d'un type de base (texte avec une limite de 100)
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSimpleType = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->simpleType
							->children('http://www.w3.org/2001/XMLSchema')->restriction;

						$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction = new ColonneRestriction();
						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_ENUMERATION:
									$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne]->m_clRestriction->m_ValeurRestriction[(string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['id']] = (string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
									break;
							}
						}
					}
					break;

			}

			if (is_null($sIDColonnePere))
				$clStructElement->m_TabStructureColonne[] = $clStructElement->m_MapIDColonne2StructColonne[$sIDColonne];
			else
				$clStructElement->m_MapIDColonne2StructColonne[$sIDColonnePere]->m_TabStructureColonne[]=$clStructElement->m_MapIDColonne2StructColonne[$sIDColonne];
		}

		if (is_null($sIDColonnePere))
			$this->m_MapIDTableau2Niv2StructureElement[$clStructElement->m_nID][$clStructElement->m_nNiveau]=$clStructElement;
	}


	protected function _ParseXML(\SimpleXMLElement $clXML)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		//on commence par faire un premier tour
		$TabFormPresent=array();
		foreach($clXML->children() as $ndRecord)
		{
			$TabFormPresent[] = str_replace('id_', '', $ndRecord->getName());
		}


		foreach($clXML->children() as $ndRecord)
		{
			$this->_ParseRecord($ndRecord, $TabFormPresent);
		}
	}

	protected function _ParseRecord(\SimpleXMLElement $clXML, $TabFormPresent)
	{
		//<id_47909919412330 simax:id="33475861129246" simax:title="Janvier">

		$TabAttrib = $clXML->attributes('http://www.nout.fr/XML/');

		$sIDTableau = str_replace('id_', '', $clXML->getName());
		$sIDEnreg = (string)$TabAttrib['id'];

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau]))
			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau] = array();

		if (!isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]))
		{
			$clRecord = new Record();
			$clRecord->m_nIDTableau = $sIDTableau;
			$clRecord->m_nIDEnreg = $sIDEnreg;
			$clRecord->m_sTitle = (string)$TabAttrib['title'];
			$clRecord->m_clStructElem = $this->clGetStructureElement($sIDTableau);

			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg] = $clRecord;
		}


		$this->__ParseColumnRecord($clXML, $TabFormPresent, $sIDTableau, $sIDEnreg);
	}

	protected function __ParseColumnRecord(\SimpleXMLElement $clXML, $TabFormPresent, $sIDTableau, $sIDEnreg)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */

		foreach($clXML->children() as $ndColonne)
		{
			$sNom = $ndColonne->getName();
			$TabAttribNOUT = $clXML->attributes('http://www.nout.fr/XML/');
			$TabAttribLayout = $clXML->attributes('http://www.nout.fr/XML/layout');

			$clInfoColonne = new InfoColonne($TabAttribNOUT, $TabAttribLayout);
			$clInfoColonne->m_nIDColonne=str_replace('id_', '', $sNom);

			if ($ndColonne->count()>0)
			{
				//on a des fils
				if (isset($this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_clStructElem))
				{
					$sTypeElement = $this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_clStructElem->sGetColonneTypeElement($clInfoColonne->m_nIDColonne);
					//on a la structure de l'enregistrement
					if ($sTypeElement==StructureColonne::TM_Separateur)
					{
						//c'est un séparateur, il faut faire les colonnes filles
						$this->__ParseColumnRecord($ndColonne, $TabFormPresent, $sIDTableau, $sIDEnreg);
					}
					else
					{
						//c'est pas un séparateur, cela devrait normalement être un TM_ListeElem
						if ($sTypeElement==StructureColonne::TM_ListeElem)
						{
							//il faut prendre la valeur des colonnes filles et mettre dans un tableau
							$clInfoColonne->m_Valeur = array();
							foreach($ndColonne->children() as $ndValeur)
							{
								$clInfoColonne->m_Valeur[]=(string)$ndValeur;
							}
						}
					}
				}
				else
				{
					//on a pas la structure
					//on regarde si le fils est dans le tableau d'element premier niveau $TabFormPresent
					$bFormulaire = false;
					foreach($ndColonne->children() as $ndFils)
					{
						$sID = str_replace('id_', '', $ndFils->getName());
						if (array_search($sID, $TabFormPresent)==false)
							$bFormulaire=true;

						break;
					}

					if ($bFormulaire)
					{
						//c'est pas un separateur mais une colonne liste
						$clInfoColonne->m_Valeur = array();
						foreach($ndColonne->children() as $ndValeur)
						{
							$clInfoColonne->m_Valeur[]=(string)$ndValeur;
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
				$clInfoColonne->m_Valeur = (string)$ndColonne;
			}

			$this->m_MapIDTableau2IDEnreg2Record[$sIDTableau][$sIDEnreg]->m_TabColumns[$clInfoColonne->m_nIDColonne]=$clInfoColonne;
		}
	}



	public function InitFromXmlXsd($nNiveau, \SimpleXMLElement $clXML, \SimpleXMLElement $clSchema=null)
	{
		//on commence par les schemas
		if (!is_null($clSchema))
		{
			$this->m_MapIDTableau2Niv2StructureElement = array();
			$this->_ParseXSD($nNiveau, $clSchema);
		}

		//après, on fait le XML
		$this->m_MapIDTableau2IDEnreg2Record = array();
		$this->_ParseXML($clXML);
	}
}