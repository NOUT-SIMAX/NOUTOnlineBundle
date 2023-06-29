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
    protected string $nID;

    /**
     * @var string
     */
    protected string $sLibelle;

    /**
     * @var StructureSection
     */
    protected StructureSection $clFiche;

    /**
     * les boutons qu'on affiche dans la fiche ou en bas des listes
     * @var StructureBouton[]
     */
    protected array $aTabBouton =[];

    /**
     * @var StructureBouton[]
     */
    protected array $aTabBoutonSurLigne =[];

    /**
     * @var StructureBouton[]
     */
    protected array $aTabBoutonColNonDetail =[];

    /**
     * les boutons qu'on affiche dans la fiche ou en bas des listes qui sont en lecture seule
     * @var StructureBouton[]
     */
    protected array $aTabBoutonReadOnly =[];

    /**
     * les boutons de remplacement qui remplace enregistrer et annuler
     * @var StructureBouton[]
     */
    protected array $aTabBoutonRemplacementValidation =[];

    /**
     * les autres boutons de remplacement
     * @var StructureBouton[]
     */
    protected array $aTabBoutonRemplacementAutre =[];

    /**
     * @var StructureColonne[]
     */
    protected array $aMapIDColonne2Structure = [];

    /**
     * @var int
     */
    protected int $eMultiColMode = StructureSection::MODE_1COlONNE;

    /**
     * @var int
     */
    protected int $eMultiColWay = StructureSection::SENS_HORIZONTAL;

    /**
     * @var string
     */
    protected string $sBackgroundColor = '';

    /** @var string  */
    protected string $nIDIcon = '';

    /** @var int  */
    protected int $nTableInfoConfiguration = 0;

    /**
     * @param $sID
     * @param $sLibelle
     * @param $bWithGhost
     */
    public function __construct($sID, $sLibelle)
    {
        $this->nID       = $sID;
        $this->sLibelle  = $sLibelle;
        $this->clFiche = new StructureSection('1', new \SimpleXMLElement('<root/>'), new \SimpleXMLElement('<root/>'));
    }

    /**
     * @param int    $eMode
     * @param int    $eWay
     * @param string $bgColor
     * @return $this
     */
    public function setMultiColonneInfo(int $eMode, int $eWay, string $bgColor): StructureElement
    {
        $this->eMultiColMode      = $eMode;
        $this->eMultiColWay       = $eWay;
        $this->sBackgroundColor = $bgColor;
        return $this;
    }

    /**
     * @return int
     */
    public function eGetMultiColonneMode(): int
    {
        return $this->eMultiColMode;
    }


    /**
     * @return int
     */
    public function eGetMultiColonneWay(): int
    {
        return $this->eMultiColWay;
    }

    /**
     * @return string
     */
    public function getBackgroundColor(): string
    {
        return $this->sBackgroundColor;
    }

    /**
     * @return string
     */
    public function getIDIcon(): string
    {
        return $this->nIDIcon;
    }

    /**
     * @return int
     */
    public function getTableInfoConfiguration(): int
    {
        return $this->nTableInfoConfiguration;
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
                    $this->nTableInfoConfiguration = (int)$clNoeud;
                    break;
                case self::OPTION_tableIconID:
                    $this->nIDIcon = (string)$clNoeud;
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

        $this->aMapIDColonne2Structure[$clStructColonne->getIDColonne()] =$clStructColonne;
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
                $this->aTabBoutonSurLigne[] = $clStructBouton;
            }

            //c'est un bouton colonne, il faut voir si c'est un bouton de substitution
            if ($clStructBouton->isOption(StructureColonne::OPTION_Substitution))
            {
                //on le met dans les boutons de substitution, pas dans les colonnes
                switch($clStructBouton->getOption(StructureColonne::OPTION_Substitution))
                {
                case StructureColonne::BTNSUB_Imprimer:
                {
                    $this->aTabBoutonRemplacementAutre[] =$clStructBouton;
                    break;
                }

                case StructureColonne::BTNSUB_Enregistrer:
                case StructureColonne::BTNSUB_Annuler:
                {
                    $this->aTabBoutonRemplacementValidation[] =$clStructBouton;
                    break;
                }

                }

                return false;
            }

            if (!$clStructBouton->isOption(StructureColonne::OPTION_DisplayOnLine)
                && !$clStructBouton->isOption(StructureColonne::OPTION_Detail))
            {
                $this->aTabBoutonColNonDetail[] = $clStructBouton;
            }

            //sinon on le met avec les autres colonnes
            $this->aMapIDColonne2Structure[$clStructBouton->getIDColonne()] =$clStructBouton;
            return true;
        }

        //c'est pas un bouton colonne, on le mets avec les boutons d'action de fiche
        $this->aTabBouton[] = $clStructBouton;
        if($clStructBouton->isReadOnly())
        {
            $this->aTabBoutonReadOnly[] = $clStructBouton;
        }

        return false;
    }


    /**
     * @param string $sIDColonne identifiant de la colonne
     * @return null|string
     */
    public function getTypeElement(string $sIDColonne) :?string
    {
        if (!isset($this->aMapIDColonne2Structure[$sIDColonne]))
        {
            return null;
        }

        return $this->aMapIDColonne2Structure[$sIDColonne]->getTypeElement();
    }

    /**
     * @param string $sIDColonne identifiant de la colonne
     * @return StructureColonne|null
     */
    public function getStructureColonne(string $sIDColonne) :?StructureColonne
    {
        if (!isset($this->aMapIDColonne2Structure[$sIDColonne]))
        {
            return null;
        }

        return $this->aMapIDColonne2Structure[$sIDColonne];
    }

    /**
     * @param string $option
     * @return array
     */
    public function filterStructureColonne(string $option) : array
    {
        return array_filter($this->aMapIDColonne2Structure, function($clStructureColonne) use($option){
            return $clStructureColonne->isOption($option);
        });
    }


    /**
     * @param $sIDColonne   string
     * @param $eTypeElement string
     */
    public function setTypeElement(string $sIDColonne, string $eTypeElement)
    {
        if (isset($this->aMapIDColonne2Structure[$sIDColonne]))
        {
            $this->aMapIDColonne2Structure[$sIDColonne]->setTypeElement($eTypeElement);
        }
    }

    /**
     * @param                    $sIDColonne string
     * @param ColonneRestriction $clRestriction
     */
    public function setRestriction(string $sIDColonne, ColonneRestriction $clRestriction)
    {
        if (isset($this->aMapIDColonne2Structure[$sIDColonne]))
        {
            $this->aMapIDColonne2Structure[$sIDColonne]->setRestriction($clRestriction);
        }
    }

    /**
     * @return string
     */
    public function getID(): string
    {
        return $this->nID;
    }

    /**
     * @return StructureSection
     */
    public function getFiche(): StructureSection
    {
        return $this->clFiche;
    }

    /**
     * @param $isReadOnly
     * @return array
     */
    public function getTabBouton($isReadOnly): array
    {
        if($isReadOnly)
        {
            return $this->aTabBoutonReadOnly;
        }

        // Sinon on récupère tous les boutons d'actions de liste par défaut
        return $this->aTabBouton;
    }

    /**
     * @return array
     */
    public function getTabBtnColNonDetail() : array
    {
        return $this->aTabBoutonColNonDetail;
    }

    /**
     * @return array
     */
    public function getTabBtnSurLigne() : array
    {
        return $this->aTabBoutonSurLigne;
    }

    /**
     * @return array
     */
    public function getTabBtnRemplacementValidation(): array
    {
        return $this->aTabBoutonRemplacementValidation;
    }

    /**
     * @return array
     */
    public function getTabBtnRemplacementAutre(): array
    {
        return $this->aTabBoutonRemplacementAutre;
    }

    /**
     * @return string
     */
    public function getLibelle(): string
    {
        return $this->sLibelle;
    }

    /**
     * @return array
     */
    public function getTabIDColonne(): array
    {
        return array_keys($this->aMapIDColonne2Structure);
    }

    /**
     * @return array
     */
    public function getMapIDColonne2Structure(): array
    {
        return $this->aMapIDColonne2Structure;
    }

    /**
     * @param $option
     * @return array
     */
    public function getTabColonneAvecOption($option): array
    {
        $aRet = array();
        foreach($this->aMapIDColonne2Structure as $clStructureColonne)
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
        foreach($this->aMapIDColonne2Structure as $clStructureColonne)
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
