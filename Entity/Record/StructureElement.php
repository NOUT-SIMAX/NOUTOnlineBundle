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
	 * @var array
	 */
	protected $m_TabBouton;

    /**
     * les boutons qu'on affiche dans la fiche ou en bas des listes qui sont en lecture seule
	 * @var array
	 */
	protected $m_TabBoutonReadOnly;

    /**
     * les boutons de remplacement qui remplace enregistrer et annuler
     * @var array
     */
    protected $m_TabBoutonRemplacementValidation;

    /**
     * les autres boutons de remplacement
     * @var array
     */
    protected $m_TabBoutonRemplacementAutre;

	/**
	 * @var StructureColonne[]
	 */
	protected $m_MapIDColonne2Structure;

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
    protected $m_sBackgroundColor;


	/**
	 * @param $sID
	 * @param $sLibelle
	 * @param $bWithGhost
	 */
	public function __construct($sID, $sLibelle)
	{
		$this->m_nID                        = $sID;
		$this->m_sLibelle                   = $sLibelle;
		$this->m_clFiche					= new StructureSection('1', new \SimpleXMLElement('<root/>'), new \SimpleXMLElement('<root/>'));
		$this->m_eMultiColMode              = StructureSection::MODE_1COlONNE;
		$this->m_eMultiColWay               = StructureSection::SENS_HORIZONTAL;
		$this->m_sBackgroundColor           = '';

		$this->m_MapIDColonne2Structure 			= array();
        $this->m_TabBouton							= array();
        $this->m_TabBoutonReadOnly					= array();
        $this->m_TabBoutonRemplacementAutre			= array();
        $this->m_TabBoutonRemplacementValidation	= array();
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
	 * @return array
	 */
	public function getColButtons(): array
    {
        $fiche                      = $this->getFiche(); // Les boutons non détail sont dans la fiche ?
        $structureColonne           = $fiche->getTabStructureColonne();

        $actionButtonsArray = array();

        // Appel à fonction récursive pour chercher les boutons dans l'arbre
        $actionButtonsArray = $this->_extractButtonsFromSection($structureColonne, $actionButtonsArray);

        return $actionButtonsArray;
	}

    /*
    * La fonction est aussi dans TransformViewWebixJSON.php
    */
    private function _extractButtonsFromSection($colonne, $actionButtonsArray)
    {
        foreach ($colonne as $element)
        {
            /* @var $element StructureDonnee */
            $typeElement = $element->getTypeElement();

            if ($typeElement == StructureColonne::TM_Bouton)
            {
                $actionButtonsArray[] = $element;
            }
            else if($element instanceof StructureSection)
            {
                $this->_extractButtonsFromSection($element, $actionButtonsArray);
            }
        }

        return $actionButtonsArray;
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
	public function getTabColonneTmTab(): array
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
}
