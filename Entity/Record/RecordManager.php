<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 10:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;

/**
 * Class RecordManager
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class RecordManager
{
	public $m_MapIDTableau2Niv2StructureElement;


	/**
	 * @param $nNiveau
	 * @param \SimpleXMLElement $clSchema
	 * @param array $MapStructureElement
	 * @return array
	 */
	protected function _s_aParseXSD($nNiveau, \SimpleXMLElement $clSchema, $MapStructureElement=array())
	{
		//récupération du noeud element fils de schema
		$ndElement = $clSchema->children('http://www.w3.org/2001/XMLSchema')->element;

		$TabAttribXS = $ndElement->attributes('http://www.w3.org/2001/XMLSchema');
		$TabAttribSIMAX = $ndElement->attributes('http://www.nout.fr/XMLSchema');

		//on récupère l'id du formulaire, son libelle et le type
		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);

		if (isset($MapStructureElement[$sIDTableau]) && isset($MapStructureElement[$sIDTableau][$nNiveau]))
			return $MapStructureElement; //on a déjà parsé cette partie de l'XSD

		$clStructureElement = new StructureElement();
		$clStructureElement->m_nNiveau = $nNiveau;
		$clStructureElement->m_nID = $sIDTableau;
		$clStructureElement->m_sLibelle = (string)$TabAttribSIMAX['name'];

		//$sType = $TabAttribSIMAX['tableType'];

		$ndSequence = $ndElement->children('http://www.w3.org/2001/XMLSchema')->complexType
								->children('http://www.w3.org/2001/XMLSchema')->sequence;

		$MapStructureElement = $this->__s_aParseXSDSequence($clStructureElement, $MapStructureElement, $ndSequence);
		return $MapStructureElement;
	}

	/**
	 * @param $clStructCurrent (StructureElement ou StructureColonne)
	 * @param $MapStructureElement
	 * @param \SimpleXMLElement $clSequence
	 * @return mixed
	 */
	protected function __s_aParseXSDSequence( $clStructCurrent, $MapStructureElement, \SimpleXMLElement $clSequence)
	{
		//'http://www.nout.fr/XMLSchema'

		foreach($clSequence->children('http://www.w3.org/2001/XMLSchema') as $ndNoeud)
		{
			if ($ndNoeud->getName() != 'element')
				continue; //je cherche quel les xs:element

			$TabAttribXS = $ndNoeud->attributes('http://www.w3.org/2001/XMLSchema');
			$TabAttribSIMAX = $ndNoeud->attributes('http://www.nout.fr/XMLSchema');

			$clStructureColonne = new StructureColonne(str_replace('id_', '', $TabAttribXS['name']), $TabAttribSIMAX);

			switch($clStructureColonne->m_eTypeElement)
			{
				case StructureColonne::TM_ListeElem:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
												->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$MapStructureElement = $this->_s_aParseXSD(StructureElement::NV_XSD_SousEnreg, $ndSequence, $MapStructureElement);
					}
					break;
				case StructureColonne::TM_Separateur:
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSequence = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->complexType
							->children('http://www.w3.org/2001/XMLSchema')->sequence;

						$MapStructureElement = $this->__s_aParseXSDSequence($clStructureColonne, $MapStructureElement, $ndSequence);
					}
					break;
				case '':
					//il faut vérifier si pas extension d'un type de base (texte avec une limite de 100)
					if (count($ndNoeud->children('http://www.w3.org/2001/XMLSchema'))>0)
					{
						$ndSimpleType = $ndNoeud  ->children('http://www.w3.org/2001/XMLSchema')->simpleType
							->children('http://www.w3.org/2001/XMLSchema')->restriction;

						$clStructureColonne->m_eTypeElement = (string)$ndSimpleType->attributes('http://www.w3.org/2001/XMLSchema')['base'];

						$clStructureColonne->m_clRestriction = new ColonneRestriction();

						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructureColonne->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_MAXLENGTH:
									$clStructureColonne->m_clRestriction->m_ValeurRestriction = (int)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
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

						$clStructureColonne->m_clRestriction = new ColonneRestriction();
						foreach($ndSimpleType->children('http://www.w3.org/2001/XMLSchema') as $ndFils)
						{
							$clStructureColonne->m_clRestriction->m_sTypeRestriction = $ndFils->getName();
							switch($ndFils->getName())
							{
								case ColonneRestriction::R_ENUMERATION:
									$clStructureColonne->m_clRestriction->m_ValeurRestriction[(string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['id']] = (string)$ndFils->attributes('http://www.w3.org/2001/XMLSchema')['value'];
									break;
							}
						}
					}
					break;

			}


			$clStructCurrent->m_TabStructureColonne[]=$clStructureColonne;
		}

		if ($clStructCurrent instanceof StructureElement)
			$MapStructureElement[$clStructCurrent->m_nID][$clStructCurrent->m_nNiveau]=$clStructCurrent;
		return $MapStructureElement;
	}


	protected static function _s_aParseXML(\SimpleXMLElement $clXML)
	{
		/*
		 * xmlns:simax="http://www.nout.fr/XML/"
         * xmlns:simax-layout="http://www.nout.fr/XML/layout"
		 */


		//récupération du noeud element fils de schema
//		$ndElement = $clSchema->children('http://www.w3.org/2001/XMLSchema', true)->element;
//
//		$TabAttribXS = $ndElement->attribut('http://www.w3.org/2001/XMLSchema');
//		$TabAttribSIMAX = $ndElement->attribut('http://www.nout.fr/XMLSchema');
//
//		//on récupère l'id du formulaire, son libelle et le type
//		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);
//
//		if (isset($MapStructureElement[$sIDTableau]) && isset($MapStructureElement[$sIDTableau][$nNiveau]))
//			return $MapStructureElement; //on a déjà parsé cette partie de l'XSD
//
//		$clStructureElement = new StructureElement();
//		$clStructureElement->m_nNiveau = $nNiveau;
//		$clStructureElement->m_nID = $sIDTableau;
//		$clStructureElement->m_sLibelle = $TabAttribSIMAX['name'];
//
//		//$sType = $TabAttribSIMAX['tableType'];
//
//		$ndSequence = $ndElement->children('http://www.w3.org/2001/XMLSchema')->complexType
//			->children('http://www.w3.org/2001/XMLSchema')->sequence;
//
//		$MapStructureElement = self::__s_aParseXSDSequence($clStructureElement, $MapStructureElement, $ndSequence);
//		return $MapStructureElement;

	}



	public function InitFromXmlXsd($nNiveau, OptionDialogue $clOptionDialogue, \SimpleXMLElement $clXML, \SimpleXMLElement $clSchema=null)
	{
		//on commence par les schemas
		if (!is_null($clSchema))
			$this->m_MapIDTableau2Niv2StructureElement = $this->_s_aParseXSD($nNiveau, $clSchema);

		//après, on fait le XML


	}

	protected function aInitStructure(\SimpleXMLElement $clSchema)
	{



	}
} 