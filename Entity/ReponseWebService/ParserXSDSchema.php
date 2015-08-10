<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:05
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureBouton;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;

class ParserXSDSchema extends Parser
{
	/**
	 * @var array
	 * map qui contient la structure des formulaires
	 */
	protected $m_MapIDTableauNiv2StructureElement;

	/**
	 * @param $sIDTableau
	 * @return StructureElement
	 */
	public function clGetStructureElement($sIDTableau)
	{
		$aNiv = array(StructureElement::NV_XSD_Enreg, StructureElement::NV_XSD_List);
		foreach($aNiv as $nNiv)
		{
			if (isset($this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv]))
			{
				return $this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv];
			}
		}

		return null;
	}


	public function Parse($nNiv, \SimpleXMLElement $clSchema)
	{
		$this->m_MapIDTableauNiv2StructureElement=array();

		//récupération du noeud element fils de schema
		$ndElement = $clSchema->children(self::NAMESPACE_XSD)->element;
		$this->_clParseXSDElementComplex($nNiv, $ndElement);
	}

	/**
	 * @param \SimpleXMLElement $ndElement
	 * @return StructureElement|null
	 */
	protected function _clParseXSDElementComplex($nNiv, \SimpleXMLElement $ndElement)
	{
		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndElement->children(self::NAMESPACE_XSD))==0)
		{
			return null;
		}

		$TabAttribXS    = $ndElement->attributes(self::NAMESPACE_XSD);

		//on récupère l'id du formulaire, son libelle et le type
		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);

		$TabAttribSIMAX = $ndElement->attributes(self::NAMESPACE_NOUT_XSD);
		$clStructureElement = new StructureElement($sIDTableau, (string) $TabAttribSIMAX['name']);

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndElement->children(self::NAMESPACE_XSD))>0)
		{
			$ndSequence = $ndElement->children(self::NAMESPACE_XSD)->complexType
									->children(self::NAMESPACE_XSD)->sequence;
			$this->__ParseXSDSequence($nNiv, $clStructureElement, $clStructureElement->getFiche(), $ndSequence);
		}


		$this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv]=$clStructureElement;
		return $clStructureElement;
	}



	/**
	 * @param StructureSection $clStructSection
	 * @param \SimpleXMLElement $clSequence
	 */
	protected function __ParseXSDSequence($nNiv, StructureElement $clStructElem, StructureSection $clStructSection, \SimpleXMLElement $clSequence)
	{
		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($clSequence->children(self::NAMESPACE_XSD))==0)
		{
			//pas de fils, on sort
			return;
		}

		foreach ($clSequence->children(self::NAMESPACE_XSD) as $ndNoeud)
		{
			if ($ndNoeud->getName() != 'element')
			{
				//je cherche quel les xs:element
				continue;
			}
			$eTypeElement = (string)$ndNoeud->attributes(self::NAMESPACE_NOUT_XSD)['typeElement'];

			$clAttribXS   = $ndNoeud->attributes(self::NAMESPACE_XSD);
			$clAttribNOUT = $ndNoeud->attributes(self::NAMESPACE_NOUT_XSD);

			$sIDColonne = str_replace('id_', '', (string)$clAttribXS->name);

			switch ($eTypeElement)
			{
				//c'est un bouton
				case StructureColonne::TM_Bouton:
				{
					$clStructureBouton = new StructureBouton($clAttribNOUT, $clAttribXS);

					if (empty($clStructureBouton->getIDColonne())) //si vide, c'est un bouton d'action sur le formulaire (supprimer, imprimer...)
					{
						$clStructElem->addButton($clStructureBouton);
						break;
					}
					$clStructSection->addColonne($clStructureBouton);
					$clStructElem->addColonne($clStructureBouton);
					break;
				}

				//c'est un séparateur
				case StructureColonne::TM_Separateur:
				{
					$clStructureSousSection = new StructureSection($sIDColonne, $clAttribNOUT, $clAttribXS);

					$ndSequence = $ndNoeud->children(self::NAMESPACE_XSD)->complexType
						->children(self::NAMESPACE_XSD)->sequence;
					$this->__ParseXSDSequence($nNiv, $clStructElem, $clStructureSousSection, $ndSequence);

					$clStructSection->addColonne($clStructureSousSection);
					$clStructElem->addColonne($clStructureSousSection);
					break;
				}

				default:
				{
					$clStructColonne = new StructureDonnee($sIDColonne, $clAttribNOUT, $clAttribXS);


					$aType2Methode = array(
						StructureColonne::TM_Combo     => '_ParseXSDCombo',
						StructureColonne::TM_ListeElem => '_ParseXSDListeElem',
						//StructureColonne::TM_Tableau   => '_ParseXSDTableau',
						''                             => '_ParseXSDColonne',
					);

					//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
					if ((count($ndNoeud->children(self::NAMESPACE_XSD))>0) && array_key_exists($eTypeElement, $aType2Methode))
					{
						$this->$aType2Methode[$eTypeElement]($nNiv, $clStructColonne, $ndNoeud);
					}

					$clStructSection->addColonne($clStructColonne);
					$clStructElem->addColonne($clStructColonne);
					break;
				}
			} // \switch
		}// \foreach fils de le séquence
	}


//	/**
//	 * @param StructureColonne  $structColonne
//	 * @param \SimpleXMLElement $ndNoeud
//	 */
//	protected function _ParseXSDTableau($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud)
//	{
//
//	}


	/**
	 * @param StructureColonne  $structColonne
	 * @param \SimpleXMLElement $ndNoeud
	 */
	protected function _ParseXSDListeElem($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndElement)
	{
		$ndElementLie = $ndElement->children(self::NAMESPACE_XSD)->complexType
			->children(self::NAMESPACE_XSD)->sequence
			->children(self::NAMESPACE_XSD)->element;

		$clStructureElemLie = $this->_clParseXSDElementComplex($nNiv+1, $ndElementLie);
		if (!is_null($clStructureElemLie))
		{
			$clStructColonne->setStructureElementLie($clStructureElemLie);
		}

	}

	/**
	 * @param \SimpleXMLElement $clXML
	 */
	protected function _ParseXSDColonne($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud)
	{
		$ndSimpleType = $ndNoeud->children(self::NAMESPACE_XSD)->simpleType
			->children(self::NAMESPACE_XSD)->restriction;

		$clStructColonne->setTypeElement((string) $ndSimpleType->attributes(self::NAMESPACE_XSD)['base']);

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndSimpleType->children(self::NAMESPACE_XSD))>0)
		{
			$clRestriction = new ColonneRestriction();

			foreach ($ndSimpleType->children(self::NAMESPACE_XSD) as $ndFils)
			{
				$clRestriction->setTypeRestriction($ndFils->getName());
				switch ($ndFils->getName())
				{
					case ColonneRestriction::R_MAXLENGTH:
						$clRestriction->setValeurRestriction((int) $ndFils->attributes(self::NAMESPACE_XSD)['value']);
						break;
				}
			}

			$clStructColonne->setRestriction($clRestriction);
		}
	}

	/**
	 * @param StructureColonne  $structColonne
	 * @param \SimpleXMLElement $ndNoeud
	 */
	protected function _ParseXSDCombo($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud)
	{
		$ndSimpleType = $ndNoeud->children(self::NAMESPACE_XSD)->simpleType
			->children(self::NAMESPACE_XSD)->restriction;

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if (count($ndSimpleType->children(self::NAMESPACE_XSD))>0)
		{
			$clRestriction = new ColonneRestriction();

			foreach ($ndSimpleType->children(self::NAMESPACE_XSD) as $ndFils)
			{
				$clRestriction->setTypeRestriction($ndFils->getName());
				switch ($ndFils->getName())
				{
					case ColonneRestriction::R_ENUMERATION:
						$clRestriction->addValeurRestriction(
							(string) $ndFils->attributes(self::NAMESPACE_XSD)['id'],
							(string) $ndFils->attributes(self::NAMESPACE_XSD)['value'],
							(string) $ndFils->attributes(self::NAMESPACE_NOUT_XSD)['icon']);
						break;
				}
			}

			$clStructColonne->setRestriction($clRestriction);
		}
	}
}