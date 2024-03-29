<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:05
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureBouton;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;

class ParserXSDSchema extends AbstractParser
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
    public function clGetStructureElement($sIDTableau, $nNiv=null): ?StructureElement
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

    /**
     * @param                   $nNiv
     * @param \SimpleXMLElement $clSchema
     * @throws \Exception
     */
    public function Parse($nNiv, \SimpleXMLElement $clSchema)
    {
        $this->m_MapIDTableauNiv2StructureElement=array();

        //récupération du noeud element fils de schema
        if (!$clSchema->count()==0)
        {
            $ndElement = $clSchema->children(self::NAMESPACE_XSD)->element;
            $this->_clParseXSDElementComplex($nNiv, $ndElement);
        }
    }

    /**
     * @param $nNiv
     * @param \SimpleXMLElement $ndElement
     * @return StructureElement|null
     * @throws \Exception
     */
    protected function _clParseXSDElementComplex($nNiv, \SimpleXMLElement $ndElement): ?StructureElement
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
        $clStructureElement = new StructureElement($sIDTableau, (string) $TabAttribSIMAX['name']);
        $clStructureElement->initOptions($TabAttribSIMAX);

        if (isset($TabAttribSIMAX[StructureSection::OPTION_ModeMultiC]) && isset($TabAttribSIMAX[StructureSection::OPTION_SensMultiC])){
            $clStructureElement->setMultiColonneInfo((int)$TabAttribSIMAX[StructureSection::OPTION_ModeMultiC], (int)$TabAttribSIMAX[StructureSection::OPTION_SensMultiC], (string)$TabAttribSIMAX[StructureSection::OPTION_BackgroundColor]);
        }

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
     * @throws \Exception
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

                case StructureColonne::TM_ListeElem:
                {
                    $clStructColonne = new StructureDonnee($sIDColonne, $clAttribNOUT, $clAttribXS);

                    if ($ndNoeud->children(self::NAMESPACE_XSD)->count()>0){
                        $this->_ParseXSDListeElem($nNiv, $clStructColonne, $ndNoeud);
                    }

                    $clStructSection->addColonne($clStructColonne);
                    $clStructElem->addColonne($clStructColonne);
                    break;
                }
                default:
                {
                    $clStructColonne = new StructureDonnee($sIDColonne, $clAttribNOUT, $clAttribXS);

                    if ($ndNoeud->children(self::NAMESPACE_XSD)->count()>0){
                        $this->_ParseXSDRestriction($clStructColonne, $ndNoeud, empty($eTypeElement));
                    }

                    $clStructSection->addColonne($clStructColonne);
                    $clStructElem->addColonne($clStructColonne);
                    break;
                }
            } // \switch
        }// \foreach fils de le séquence
    }

    /**
     * @param $nNiv
     * @param StructureColonne  $clStructColonne
     * @param \SimpleXMLElement $ndElement
     * @throws \Exception
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
     * @param StructureColonne $clStructColonne
     * @param \SimpleXMLElement $ndNoeud
     * @param $replaceTypeElement
     */
    protected function _ParseXSDRestriction(StructureColonne $clStructColonne, \SimpleXMLElement $ndNoeud, $replaceTypeElement)
    {
        $ndSimpleType = $ndNoeud->children(self::NAMESPACE_XSD)->simpleType;
        if (is_null($ndSimpleType) || (count($ndSimpleType)==0)) {
            return ;
        }

        $ndRestriction = $ndSimpleType->children(self::NAMESPACE_XSD)->restriction;

        if (is_null($ndRestriction) || (count($ndRestriction)==0)){
            return ;
        }

        if ($replaceTypeElement){
            $clStructColonne->setTypeElement((string) $ndRestriction->attributes(self::NAMESPACE_XSD)['base']);
        }

        $clRestriction = null;

        //ne pas mettre empty car ce n'est pas un array mais un \SimpleXMLElement et empty ne marche pas dessus
        if (count($ndRestriction->children(self::NAMESPACE_XSD))>0)
        {
            $clRestriction = new ColonneRestriction();
            foreach ($ndRestriction->children(self::NAMESPACE_XSD) as $ndFils)
            {
                switch ($ndFils->getName())
                {
                    case ColonneRestriction::R_WHITESPACE:
                        $clRestriction->addRestrictionSimple($ndFils->getName(), (string) $ndFils->attributes(self::NAMESPACE_XSD)['value']);
                        break;
                    case ColonneRestriction::R_LENGTH:
                    case ColonneRestriction::R_MAXLENGTH:
                        $clRestriction->addRestrictionSimple($ndFils->getName(), (int) $ndFils->attributes(self::NAMESPACE_XSD)['value']);
                        break;
                    case ColonneRestriction::R_ENUMERATION:
                        $clRestriction->addRestrictionArray($ndFils->getName(),
                            (string) $ndFils->attributes(self::NAMESPACE_XSD)['id'],
                            (string) $ndFils->attributes(self::NAMESPACE_XSD)['value'],
                            (string) $ndFils->attributes(self::NAMESPACE_NOUT_XSD)['icon']);
                        break;
                }
            }
        }

        if ($ndRestriction->children(self::NAMESPACE_NOUT_XSD)->count()>0)
        {
            $clRestriction = $clRestriction ? $clRestriction : new ColonneRestriction();

            foreach ($ndRestriction->children(self::NAMESPACE_NOUT_XSD) as $ndFils)
            {
                switch ($ndFils->getName())
                {
                    case ColonneRestriction::R_NumericDisplay:
                        //les infos sur la taille, la forme et l'affichage de la valeur
                        foreach($ndFils->attributes(self::NAMESPACE_NOUT_XSD) as $key => $value) {
                            $clRestriction->addRestrictionSimple($key,(string) $value);
                        }

                        //le découpage en pallier s'ils existent
                        if (count($ndFils->children(self::NAMESPACE_NOUT_XSD)->{ColonneRestriction::R_NumericDisplay_Stage})){
                            $aStages = array();
                            foreach($ndFils->children(self::NAMESPACE_NOUT_XSD)->{ColonneRestriction::R_NumericDisplay_Stage} as $ndStage)
                            {
                                $stageOption = new \stdClass();
                                foreach ($ndStage->attributes(self::NAMESPACE_NOUT_XSD) as $key => $value) {
                                    $stageOption->$key = strcmp($key, 'value')==0 ?  (float) $value : (string) $value;
                                }
                                $aStages[]=$stageOption;
                            }
                            $clRestriction->addRestrictionSimple(ColonneRestriction::R_NumericDisplay_Stage, $aStages);
                        }
                        break;
                    case ColonneRestriction::R_NumericEditType:
                        $clRestriction->addRestrictionSimple($ndFils->getName(), (string)$ndFils);
                        break;
                }
            }
        }

        if ($clRestriction){
            $clStructColonne->setRestriction($clRestriction);
        }
    }
}
