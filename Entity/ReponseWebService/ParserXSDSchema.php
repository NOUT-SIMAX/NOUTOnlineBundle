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
     * @param $nNiv
	 * @return StructureElement
	 */
	public function clGetStructureElement($sIDTableau, $nNiv=null)
	{
        if (!is_null($nNiv))
        {
            if (isset($this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv]))
            {
                return $this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv];
            }

            return null;
        }

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
     * @param $nNiv
	 * @param \SimpleXMLElement $ndElement
	 * @return StructureElement|null
	 */
	protected function _clParseXSDElementComplex($nNiv, \SimpleXMLElement $ndElement)
	{
		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if ($ndElement->children(self::NAMESPACE_XSD)->count()==0)
		{
			return null;
		}

		$TabAttribXS    = $ndElement->attributes(self::NAMESPACE_XSD);

		//on récupère l'id du formulaire, son libelle et le type
		$sIDTableau = str_replace('id_', '', $TabAttribXS['name']);

		$TabAttribSIMAX = $ndElement->attributes(self::NAMESPACE_NOUT_XSD);
		$clStructureElement = new StructureElement($sIDTableau, (string) $TabAttribSIMAX['name'], ((int) $TabAttribSIMAX['withGhost'])==1);

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if ($ndElement->children(self::NAMESPACE_XSD)->count()>0)
		{
			$ndSequence = $ndElement->children(self::NAMESPACE_XSD)->complexType
									->children(self::NAMESPACE_XSD)->sequence;
			$this->__ParseXSDSequence($nNiv, $clStructureElement, $clStructureElement->getFiche(), $ndSequence);
		}


		$this->m_MapIDTableauNiv2StructureElement[$sIDTableau.'/'.$nNiv]=$clStructureElement;
		return $clStructureElement;
	}



	/**
     * @param $nNiv
     * @param StructureElement $clStructElem
	 * @param StructureSection $clStructSection
	 * @param \SimpleXMLElement $clSequence
	 */
	protected function __ParseXSDSequence($nNiv, StructureElement $clStructElem, StructureSection $clStructSection, \SimpleXMLElement $clSequence)
	{
		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if ($clSequence->children(self::NAMESPACE_XSD)->count()==0)
		{
			//pas de fils, on sort
			return;
		}

		foreach ($clSequence->children(self::NAMESPACE_XSD) as $ndNoeud)
		{
		    /** @var \SimpleXMLElement $ndNoeud */
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
                    $ndSeqSousButtons = null;
                    try
                    {
                        if ($ndNoeud->children(self::NAMESPACE_XSD)->count() > 0)
                        {
                            $ndSeqSousButtons = $ndNoeud->children(self::NAMESPACE_XSD)->complexType->children(self::NAMESPACE_XSD)->sequence;
                            if ($ndSeqSousButtons->children(self::NAMESPACE_XSD)->count() == 0)
                            {
                                $ndSeqSousButtons = null;
                            }
                        }
                    }
                    catch (\Exception $e) {
                        $ndSeqSousButtons = null;
                    }

					$clStructureBouton = new StructureBouton($clAttribNOUT, $clAttribXS, $ndSeqSousButtons);
                    $bIsCol = $clStructElem->addButton($clStructureBouton);

					if ($bIsCol) //c'est un colonne bouton qui n'est pas un bouton de substitution, on l'ajoute à la section
					{
                        $clStructSection->addColonne($clStructureBouton);
					}
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
						StructureColonne::TM_Combo      => '_ParseXSDCombo',
						StructureColonne::TM_ListeElem  => '_ParseXSDListeElem',
						//StructureColonne::TM_Tableau  => '_ParseXSDTableau',
						''                              => '_ParseXSDColonne',
                        StructureColonne::TM_Entier     => '_ParseXSDNumeric',
                        StructureColonne::TM_Reel       => '_ParseXSDNumeric',
                        StructureColonne::TM_Monetaire  => '_ParseXSDNumeric',
					);

					//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
					if (($ndNoeud->children(self::NAMESPACE_XSD)->count()>0) && array_key_exists($eTypeElement, $aType2Methode))
					{
					    $method = $aType2Methode[$eTypeElement];
						$this->$method($nNiv, $clStructColonne, $ndNoeud);
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
     * @param $nNiv
	 * @param StructureColonne  $clStructColonne
	 * @param \SimpleXMLElement $ndElement
	 */
	protected function _ParseXSDListeElem($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndElement)
	{
		$ndSequence = $ndElement->children(self::NAMESPACE_XSD)->complexType
			->children(self::NAMESPACE_XSD)->sequence
        ;

        $nIndice = 0;
        $clStructureElemLie = null;
        foreach ($ndSequence->children(self::NAMESPACE_XSD) as $ndElement)
        {
            if ($nIndice == 0)
            {
                //c'est le premier élément
                $clStructureElemLie = $this->_clParseXSDElementComplex($nNiv+1, $ndElement);
            }
            else
            {
                //les autres éléments sont les boutons
                $eTypeElement = (string)$ndElement->attributes(self::NAMESPACE_NOUT_XSD)['typeElement'];
                if ($eTypeElement != StructureColonne::TM_Bouton)
                {
                    throw new \Exception("Ici on devrait avoir un bouton");
                }

                $clAttribXS   = $ndElement->attributes(self::NAMESPACE_XSD);
                $clAttribNOUT = $ndElement->attributes(self::NAMESPACE_NOUT_XSD);
                $ndSeqSousButtons = null;
                try
                {
                    if ($ndElement->children(self::NAMESPACE_XSD)->count() > 0)
                    {
                        $ndSeqSousButtons = $ndElement->children(self::NAMESPACE_XSD)->complexType->children(self::NAMESPACE_XSD)->sequence;
                        if ($ndSeqSousButtons->children(self::NAMESPACE_XSD)->count() == 0)
                        {
                            $ndSeqSousButtons = null;
                        }
                    }
                }
                catch (\Exception $e) {
                    $ndSeqSousButtons = null;
                }
                $clStructureBouton = new StructureBouton($clAttribNOUT, $clAttribXS, $ndSeqSousButtons);
                $bIsCol = $clStructureElemLie->addButton($clStructureBouton);
                if ($bIsCol) //si vide, c'est un bouton d'action sur le formulaire (supprimer, imprimer...)
                {
                    throw new \Exception("Ici on ne devrait pas avoir de colonne bouton");
                }
            }
            $nIndice++;
        }


		if (!is_null($clStructureElemLie))
		{
			$clStructColonne->setStructureElementLie($clStructureElemLie);
		}
	}

	/**
     * @param $nNiv
     * @param StructureColonne $clStructColonne
	 * @param \SimpleXMLElement $ndNoeud
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
				switch ($ndFils->getName())
				{
                    case ColonneRestriction::R_LENGTH:
					case ColonneRestriction::R_MAXLENGTH:
						$clRestriction->addRestrictionSimple($ndFils->getName(), (int) $ndFils->attributes(self::NAMESPACE_XSD)['value']);
						break;
                }
			}

			$clStructColonne->setRestriction($clRestriction);
		}
	}

	/**
     * @param $nNiv
	 * @param StructureColonne  $clStructColonne
	 * @param \SimpleXMLElement $ndNoeud
	 */
	protected function _ParseXSDCombo($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud)
	{
		$ndSimpleType = $ndNoeud->children(self::NAMESPACE_XSD)->simpleType
			->children(self::NAMESPACE_XSD)->restriction;

		//ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
		if ($ndSimpleType->children(self::NAMESPACE_XSD)->count()>0)
		{
			$clRestriction = new ColonneRestriction();

			foreach ($ndSimpleType->children(self::NAMESPACE_XSD) as $ndFils)
			{
				switch ($ndFils->getName())
				{
					case ColonneRestriction::R_ENUMERATION:
                        $clRestriction->addRestrictionArray($ndFils->getName(),
							(string) $ndFils->attributes(self::NAMESPACE_XSD)['id'],
							(string) $ndFils->attributes(self::NAMESPACE_XSD)['value'],
							(string) $ndFils->attributes(self::NAMESPACE_NOUT_XSD)['icon']);
						break;
				}
			}

			$clStructColonne->setRestriction($clRestriction);
		}
	}

	/**
     * @param $nNiv
     * @param StructureColonne $clStructColonne
     * @param \SimpleXMLElement $ndNoeud
     */
	protected function _ParseXSDNumeric($nNiv, StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud) {
        //TODO: Try generic code that handles any xml tree
	    $ndSimpleType = $ndNoeud->children(self::NAMESPACE_XSD)->simpleType
            ->children(self::NAMESPACE_XSD)->restriction;
        $children = $ndSimpleType->children(self::NAMESPACE_NOUT_XSD);
        $r_numericDisplay = ColonneRestriction::R_NumericDisplay;
        if(!empty($ndSimpleType->$r_numericDisplay)) {
            $ndNumericDisplay = $children->$r_numericDisplay;
            $clRestriction = new ColonneRestriction();
            foreach($ndNumericDisplay->attributes(self::NAMESPACE_NOUT_XSD) as $key => $value) {
                $clRestriction->addRestrictionSimple($key,(string) $value);
            }
            $r_numericDisplay_stage = ColonneRestriction::R_NumericDisplay_Stage;
            if(count($xsdStages = $ndNumericDisplay->children(self::NAMESPACE_NOUT_XSD)->$r_numericDisplay_stage) > 0) {
                $aStages = array();
                foreach($xsdStages as $stage) {
                    $stageOption = new \stdClass();
                    foreach ($stage->attributes(self::NAMESPACE_NOUT_XSD) as $key => $value) {
                        $stageOption->$key = (string) $value;
                    }
                    array_push($aStages, $stageOption);
                }
            }
            $clRestriction->addRestrictionSimple(ColonneRestriction::R_NumericDisplay_Stage, $aStages);
            $clStructColonne->setRestriction($clRestriction);
        }
    }
}