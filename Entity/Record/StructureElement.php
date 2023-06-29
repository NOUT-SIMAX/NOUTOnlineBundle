<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 11:42
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

class StructureElement
{
    /**
     * identifiant du formulaire
     * @var string
     */
    protected $m_nID;

    /**
     * @var string
     */
    protected $m_sLibelle;

    /**
     * @var StructureSection
     */
    protected $m_clFiche;

    /**
     * les boutons qu'on affiche dans la fiche ou en bas des listes
     * @var StructureBouton[]
     */
    protected $m_TabBouton=[];

    /**
     * @var StructureBouton[]
     */
    protected $m_TabBoutonSurLigne=[];

    /**
     * @var StructureBouton[]
     */
    protected $m_TabBoutonColNonDetail=[];

    /**
     * les boutons qu'on affiche dans la fiche ou en bas des listes qui sont en lecture seule
     * @var StructureBouton[]
     */
    protected $m_TabBoutonReadOnly=[];

    /**
     * les boutons de remplacement qui remplace enregistrer et annuler
     * @var StructureBouton[]
     */
    protected $m_TabBoutonRemplacementValidation=[];

    /**
     * les autres boutons de remplacement
     * @var StructureBouton[]
     */
    protected $m_TabBoutonRemplacementAutre=[];

    /**
     * @var StructureColonne[]
     */
    protected $m_MapIDColonne2Structure = [];

    /**
     * @var int
     */
    protected $m_eMultiColMode;

    /**
     * @var int
     */
    protected $m_eMultiColWay;

    /**
     * @var string
     */
    protected $m_sBackgroundColor = '';

    /** @var string  */
    protected $m_nIDIcon = '';

    /** @var int  */
    protected $m_nTableInfoConfiguration = 0;

    /**
     * @param $sID
     * @param $sLibelle
     * @param $bWithGhost
     */
    public function __construct($sID, $sLibelle)
    {
        $this->m_nID           = $sID;
        $this->m_sLibelle      = $sLibelle;
        $this->m_clFiche       = new StructureSection('1', new \SimpleXMLElement('<root/>'), new \SimpleXMLElement('<root/>'));
        $this->m_eMultiColMode = StructureSection::MODE_1COlONNE;
        $this->m_eMultiColWay  = StructureSection::SENS_HORIZONTAL;
    }

    /**
     * @param int    $eMode
     * @param int    $eWay
     * @param string $bgColor
     * @return $this
     */
    public function setMultiColonneInfo(int $eMode, int $eWay, string $bgColor): StructureElement
    {
        $this->m_eMultiColMode = $eMode;
        $this->m_eMultiColWay = $eWay;
        $this->m_sBackgroundColor = $bgColor;
        return $this;
    }

    /**
     * @return int
     */
    public function eGetMultiColonneMode(): int
    {
        return $this->m_eMultiColMode;
    }


    /**
     * @return int
     */
    public function eGetMultiColonneWay(): int
    {
        return $this->m_eMultiColWay;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->m_sBackgroundColor;
    }

    /**
     * @return string
     */
    public function getIDIcon(): string
    {
        return $this->m_nIDIcon;
    }

    /**
     * @return int
     */
    public function getTableInfoConfiguration(): int
    {
        return $this->m_nTableInfoConfiguration;
    }

    /**
     * @param \SimpleXMLElement $TabAttribSIMAX
     */
    public function initOptions(\SimpleXMLElement $TabAttribSIMAX)
    {
        foreach($TabAttribSIMAX as $name => $clNoeud)
        {
            switch ($name)
            {
                case self::OPTION_tableInfoConfiguration:
                    $this->m_nTableInfoConfiguration = (int)$clNoeud;
                    break;
                case self::OPTION_tableIconID:
                    $this->m_nIDIcon = (string)$clNoeud;
                    break;
            }
        }
    }


    /**
     * @param StructureColonne $clStructColonne
     * @return $this|StructureElement|void
     * @throws \Exception
     */
    public function addColonne(StructureColonne $clStructColonne): StructureElement
    {
        if ($clStructColonne instanceof StructureBouton)
        {
            throw new \Exception("StructureColonne::addColonne ne doit pas être utilisée pour ajouter des boutons, utiliser addButton à la place.");
        }

        $this->m_MapIDColonne2Structure[$clStructColonne->getIDColonne()]=$clStructColonne;
        return $this;
    }


    /**
     * ajoute un des boutons d'actions (non paramétré)
     * @param StructureBouton $clStructBouton
     * @return bool
     */
    public function addButton(StructureBouton $clStructBouton): bool
    {
        $nIDColonne = $clStructBouton->getIDColonne();
        if (!empty($nIDColonne))
        {
            if ($clStructBouton->isOption(StructureColonne::OPTION_DisplayOnLine))
            {
                $this->m_TabBoutonSurLigne[] = $clStructBouton;
            }

            //c'est un bouton colonne, il faut voir si c'est un bouton de substitution
            if ($clStructBouton->isOption(StructureColonne::OPTION_Substitution))
            {
                //on le met dans les boutons de substitution, pas dans les colonnes
                switch($clStructBouton->getOption(StructureColonne::OPTION_Substitution))
                {
                case StructureColonne::BTNSUB_Imprimer:
                {
                    $this->m_TabBoutonRemplacementAutre[]=$clStructBouton;
                    break;
                }

                case StructureColonne::BTNSUB_Enregistrer:
                case StructureColonne::BTNSUB_Annuler:
                {
                    $this->m_TabBoutonRemplacementValidation[]=$clStructBouton;
                    break;
                }

                }

                return false;
            }

            if (!$clStructBouton->isOption(StructureColonne::OPTION_DisplayOnLine)
                && !$clStructBouton->isOption(StructureColonne::OPTION_Detail))
            {
                $this->m_TabBoutonColNonDetail[] = $clStructBouton;
            }

            //sinon on le met avec les autres colonnes
            $this->m_MapIDColonne2Structure[$clStructBouton->getIDColonne()]=$clStructBouton;
            return true;
        }

        //c'est pas un bouton colonne, on le mets avec les boutons d'action de fiche
        $this->m_TabBouton[] = $clStructBouton;
        if($clStructBouton->isReadOnly())
        {
            $this->m_TabBoutonReadOnly[] = $clStructBouton;
        }

        return false;
    }


    /**
     * @param string $sIDColonne identifiant de la colonne
     * @return null|string
     */
    public function getTypeElement(string $sIDColonne) :?string
    {
        if (!isset($this->m_MapIDColonne2Structure[$sIDColonne]))
        {
            return null;
        }

        return $this->m_MapIDColonne2Structure[$sIDColonne]->getTypeElement();
    }

    /**
     * @param string $sIDColonne identifiant de la colonne
     * @return StructureColonne|null
     */
    public function getStructureColonne(string $sIDColonne) :?StructureColonne
    {
        if (!isset($this->m_MapIDColonne2Structure[$sIDColonne]))
        {
            return null;
        }

        return $this->m_MapIDColonne2Structure[$sIDColonne];
    }

    /**
     * @param string $option
     * @return array
     */
    public function filterStructureColonne(string $option) : array
    {
        return array_filter($this->m_MapIDColonne2Structure, function($clStructureColonne) use($option){
            return $clStructureColonne->isOption($option);
        });
    }


    /**
     * @param $sIDColonne   string
     * @param $eTypeElement string
     */
    public function setTypeElement(string $sIDColonne, string $eTypeElement)
    {
        if (isset($this->m_MapIDColonne2Structure[$sIDColonne]))
        {
            $this->m_MapIDColonne2Structure[$sIDColonne]->setTypeElement($eTypeElement);
        }
    }

    /**
     * @param                    $sIDColonne string
     * @param ColonneRestriction $clRestriction
     */
    public function setRestriction(string $sIDColonne, ColonneRestriction $clRestriction)
    {
        if (isset($this->m_MapIDColonne2Structure[$sIDColonne]))
        {
            $this->m_MapIDColonne2Structure[$sIDColonne]->setRestriction($clRestriction);
        }
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->m_nID;
    }

    /**
     * @return StructureSection
     */
    public function getFiche(): StructureSection
    {
        return $this->m_clFiche;
    }

    /**
     * @param $isReadOnly
     * @return array
     */
    public function getTabBouton($isReadOnly): array
    {
        if($isReadOnly)
        {
            return $this->m_TabBoutonReadOnly;
        }

        // Sinon on récupère tous les boutons d'actions de liste par défaut
        return $this->m_TabBouton;
    }

    /**
     * @return array
     */
    public function getTabBtnColNonDetail() : array
    {
        return $this->m_TabBoutonColNonDetail;
    }

    /**
     * @return array
     */
    public function getTabBtnSurLigne() : array
    {
        return $this->m_TabBoutonSurLigne;
    }

    /**
     * @return array
     */
    public function getTabBtnRemplacementValidation(): array
    {
        return $this->m_TabBoutonRemplacementValidation;
    }

    /**
     * @return array
     */
    public function getTabBtnRemplacementAutre(): array
    {
        return $this->m_TabBoutonRemplacementAutre;
    }

    /**
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->m_sLibelle;
    }

    /**
     * @return array
     */
    public function getTabIDColonne(): array
    {
        return array_keys($this->m_MapIDColonne2Structure);
    }

    /**
     * @return array
     */
    public function getMapIDColonne2Structure(): array
    {
        return $this->m_MapIDColonne2Structure;
    }

    /**
     * @param $option
     * @return array
     */
    public function getTabColonneAvecOption($option): array
    {
        $aRet = array();
        foreach($this->m_MapIDColonne2Structure as $clStructureColonne)
        {
            /** @var StructureColonne $clStructureColonne */
            if ($clStructureColonne->isOption($option))
                $aRet[]=$clStructureColonne->getIDColonne();
        }

        return $aRet;
    }

    /**
     * @return array
     */
    public function getTabColonne2IDTableauLie(): array
    {
        // OPTION_LinkedTableID // Ne marche pas
        // On cherche les éléments avec l'ID StructureColonne::TM_Tableau

        // Tableau de retour
        $aRet = array();
        foreach($this->m_MapIDColonne2Structure as $clStructureColonne)
        {
            /** @var StructureColonne $clStructureColonne */
            if ($clStructureColonne->getTypeElement() == StructureColonne::TM_Tableau)
            {
                $aRet[$clStructureColonne->getIDColonne()] = $clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID);
            }
        }

        return $aRet;
    }

    const NV_XSD_Enreg                  = 0;
    const NV_XSD_List                   = 1;
    const NV_XSD_LienElement            = 2;

    const OPTION_tableInfoConfiguration = 'tableInfoConfiguration';
    const OPTION_tableIconID            = 'tableIconID';
}
