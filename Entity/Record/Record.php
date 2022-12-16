<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 14:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ParametersManagement;

/**
 * Class Record, Description d'un enregistrement
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 *
 */
class Record extends IHMWindows
{

    /** @var string : contient le sous-titre quand il y en a un */
    protected $m_sSubTitle='';

	/** @var string : identitifant de l'enregistrement */
	protected $m_nIDEnreg='';

    /** @var array */
    protected $m_TabOptionsRecord = [];

    /** @var array */
    protected $m_TabOptionsLayout = [];

	/** @var InfoColonne[] : tableau avec les informations variables des colonnes (mise en forme ...) */
	protected $m_TabColumnsInfo = [];

	/** @var array : tableau avec les valeurs des colonnes */
	protected $m_TabColumnsValues = [];

	/** @var array : tableau de booleen pour indiquer que la valeur à changée */
	protected $m_TabColumnsModified = [];

	/** @var RecordCache */
	protected $m_clCacheRecordLie;

	/** @var int */
	protected $m_nXSDNiv=0;

    /** @var  string */
    protected $m_sLinkedTableID='';

    /**
     * @return string
     */
    public function getLinkedTableID(): string
    {
        return $this->m_sLinkedTableID;
    }

    /**
     * @param string $linkedTableID
     */
    public function setLinkedTableID(string $linkedTableID)
    {
        $this->m_sLinkedTableID = $linkedTableID;
    }

    /**
     * Record constructor.
     * @param string                $sIDTableau
     * @param string                $sIDEnreg
     * @param string                $sTitle
     * @param int                   $nNiv
     * @param StructureElement|null $clStruct
     */
	public function __construct(string $sIDTableau, string $sIDEnreg, string $sTitle, int $nNiv, StructureElement $clStruct = null)
	{
	    parent::__construct($sTitle, $sIDTableau, $clStruct);

		$this->m_nIDEnreg     = $sIDEnreg;
		$this->m_nXSDNiv      = (int)$nNiv;

		//tableau des éléments liés
		$this->m_clCacheRecordLie = new RecordCache();
	}

	/**
	 * @param Record $clRecord
	 * @return bool
	 */
	public function isBetterLevel(Record $clRecord): bool
    {
		return $this->m_nXSDNiv <= $clRecord->m_nXSDNiv;
	}

    /**
     * @return array
     */
    public function aGetTabColumnsValues(): array
    {
        return $this->m_TabColumnsValues;
    }

    /**
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOption($option, $valeur): Record
    {
        $this->m_TabOptionsRecord[$option]=$valeur;
        return $this;
    }
    /**
     * @param \SimpleXMLElement $tabAttribut
     * @return $this
     */
    public function addOptions(\SimpleXMLElement $tabAttribut): Record
    {
        foreach($tabAttribut as $name=>$attr) {
            if (in_array($name, [self::OPTION_Type, self::OPTION_Record, self::OPTION_RecorWithChildren])){
                $this->addOption($name, (string)$attr);
            }
        }
        return $this;
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->m_TabOptionsRecord;
    }

    /**
     * @param $option
     * @param mixed|null $default
     * @return mixed
     */
    public function getOption($option, $default=null)
    {
        return $this->m_TabOptionsRecord[$option] ?? $default;
    }


    /**
     * @param $option
     * @param $valeur
     * @return $this
     */
    public function addOptionLayout($option, $valeur): Record
    {
        $this->m_TabOptionsLayout[$option]=$valeur;
        return $this;
    }
    /**
     * @param \SimpleXMLElement $tabAttribut
     * @return $this
     */
    public function addOptionsLayout(\SimpleXMLElement $tabAttribut): Record
    {
        foreach($tabAttribut as $name=>$attr)
        {
            $this->addOptionLayout($name, (string)$attr);
        }
        return $this;
    }

    /**
     * @param $option
     * @param mixed|null $default
     * @return mixed
     */
    public function getOptionLayout($option, $default=null)
    {
        if (isset($this->m_TabOptionsLayout[$option]))
        {
            return $this->m_TabOptionsLayout[$option];
        }
        return $default;
    }

    /**
     * renvoi l'identifiant de l'enregistrement
     * @return string
     */
	public function getIDEnreg(): string
    {
		return $this->m_nIDEnreg;
	}

    /**
     * @return RecordCache
     */
    public function getRecordLie(): RecordCache
    {
        return $this->m_clCacheRecordLie;
    }


    /**
     * @return string
     */
    public function getSubTitle(): ?string
    {
        return $this->m_sSubTitle;
    }

    /**
     * @param string $sSubTitle
     * @return $this
     */
    public function setSubTitle(string $sSubTitle): Record
    {
        $this->m_sSubTitle = $sSubTitle;
        return $this;
    }



    /**
     * @return array
     */
    public function aGetTabOptionsLayout(): array
    {
        return $this->m_TabOptionsLayout;
    }

    /**
     * @return array|InfoColonne[]
     */
    public function aGetTabColumnsInfo(): array
    {
        return $this->m_TabColumnsInfo;
    }

	/**
	 * @param InfoColonne $clInfoColonne
	 * @return $this
	 */
	public function setInfoColonne(InfoColonne $clInfoColonne): Record
    {
		$this->m_TabColumnsInfo[$clInfoColonne->getIDColonne()] = $clInfoColonne;

		return $this;
	}

    /**
     * @param $idColonne
     * @return InfoColonne|null
     */
    public function getInfoColonne($idColonne): ?InfoColonne
    {
        if (!isset($this->m_TabColumnsInfo[$idColonne]))
        {
            return null;
        }

        return $this->m_TabColumnsInfo[$idColonne];
    }

	/**
	 * @return bool
	 */
	public function bRef(): bool
    {
		return !empty($this->m_TabColumnsInfo);
	}

	/**
     * retourne la valeur stockée
	 * @param $idColonne
	 * @return mixed
	 */
	public function getValCol($idColonne)
	{
		if (!isset($this->m_TabColumnsValues[$idColonne]))
		{
			return null;
		}

		return $this->m_TabColumnsValues[$idColonne];
	}

    /**
     * @param $idColonne
     * @return array
     */
	public function getMultilangValue($idColonne) : array
    {
        if (!isset($this->m_TabMultilangueValues[$idColonne]))
        {
            return [];
        }

        return $this->m_TabMultilangueValues[$idColonne];
    }


    /**
     * retourne le titre d'un Enreg Lié
     * @param $idRecordLie
     * @param $idColonne
     * @return string
     */
    public function getTitleFromIDRecordLie($idRecordLie, $idColonne): string
    {
        if (!isset($this->m_clCacheRecordLie) || !isset($this->m_clStructElem))
        {
            return "";
        }

        $clStructureColonne = $this->m_clStructElem->getStructureColonne($idColonne);
        $record = $this->m_clCacheRecordLie->getRecord($clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID), $idRecordLie);
        if (!isset($record)){
            return '';
        }

        return $record->getTitle();
    }



    /**
     * retourne la valeur affichée
     * @param $idColonne
     * @return mixed
     */
    public function getDisplayValCol($idColonne)
    {
        if (!isset($this->m_TabColumnsValues[$idColonne])){
            return null;
        }

        $clStructureColonne = $this->m_clStructElem->getStructureColonne($idColonne);

        switch($clStructureColonne->getTypeElement())
        {
            case StructureColonne::TM_Tableau:
            {
                $valStockee = $this->m_TabColumnsValues[$idColonne];
                if (empty($valStockee) || ($valStockee=='0'))
                {
                    return '';
                }

                $clRecordLie = $this->m_clCacheRecordLie->getRecord($clStructureColonne->getOption(StructureColonne::OPTION_LinkedTableID), $valStockee);
                /** @var Record|null $clRecordLie */
                if (is_null($clRecordLie))
                {
                    return "#{$valStockee}#";
                }
                return $clRecordLie->getTitle();
            }

            default:
            {
                return $this->m_TabColumnsValues[$idColonne];
            }
        }

    }


    /**
     * @param $idColonne
     * @param bool $byUser
     * @return bool
     */
    public function isModified($idColonne, bool $byUser): bool
    {
        if (!isset($this->m_TabColumnsModified[$idColonne])){
            return false;
        }

        return $byUser
            ? $this->m_TabColumnsModified[$idColonne] > 0
            : $this->m_TabColumnsModified[$idColonne] < 0;
    }

	/**
	 * @param string $idcolonne
	 * @param $value
	 * @param bool   $modifiedByUser
     * @param int    $codelangue
     * @param bool   $bCurrentLanguage
	 * @return $this
	 */
	public function setValCol(string $idcolonne, $value, bool $modifiedByUser = true, int $codelangue = 0, bool $bCurrentLanguage=true): Record
    {
		$this->m_TabColumnsModified[$idcolonne] = $modifiedByUser ? 1 : -1;
		$clStructColonne = $this->getStructColonne($idcolonne);
		if (isset($clStructColonne) && $clStructColonne->isOption(StructureColonne::OPTION_Modele_Multilanguage)){
            if (is_array($value)){
                $this->m_TabColumnsValues[$idcolonne] = $value;
            }
            else {
                if (!array_key_exists($idcolonne, $this->m_TabColumnsValues)){
                    $this->m_TabColumnsValues[$idcolonne]=['display' => ''];
                }
                if ($codelangue == 0){
                    $this->m_TabColumnsValues[$idcolonne]['display']=$value;
                }
                else {
                    $this->m_TabColumnsValues[$idcolonne][$codelangue]=$value;
                    if ($bCurrentLanguage){
                        $this->m_TabColumnsValues[$idcolonne]['display'] = $value;
                    }
                }
            }
        }
		else {
            $this->m_TabColumnsValues[$idcolonne] = $value;
        }
		return $this;
	}

	/**
     * @param $nNiv
	 * @param Record $clRecordLie
	 * @return $this
	 */
	public function addRecordLie($nNiv, Record $clRecordLie): Record
    {
        $this->m_clCacheRecordLie->SetRecord($nNiv, $clRecordLie);
		return $this;
	}

	/**
     * @param $nNiv
	 * @param array $aRecordsLies
	 * @return $this
	 */
	public function addTabRecordLie($nNiv, array $aRecordsLies): Record
    {
		foreach($aRecordsLies as $clRecord)
		{
            $this->addRecordLie($nNiv, $clRecord);
		}
		return $this;
	}


    /**
     * méthode magique pour les formulaires
     * @param $idColonne
     * @throws \Exception
     */
	public function __get($idColonne)
	{
		if (in_array($idColonne, array('m_sTitle', 'm_nIDEnreg', 'm_nIDTableau', 'm_TabColumnsInfo', 'm_TabColumnsValues', 'm_TabColumnsModified', 'm_clStructElem')))
		{
			throw new \Exception("Accès au membre $idColonne de ".get_class($this).'via __get() n\'est pas autorisé');
		}

		return $this->getValCol($idColonne);
	}

    /**
     * méthode magique pour les formulaires - Met à jour les valeurs des colonnes depuis les formulaires Symfony
     * @param $idColonne
     * @param $value
     * @return Record
     * @throws \Exception
     */
	public function __set($idColonne, $value)
    {
		if (in_array($idColonne, array('m_sTitle', 'm_nIDEnreg', 'm_nIDTableau', 'm_TabColumnsInfo', 'm_TabColumnsValues', 'm_TabColumnsModified', 'm_clStructElem')))
		{
			throw new \Exception("Accès au membre $idColonne de ".get_class($this).'via __call() n\'est pas autorisé');
		}

		return $this->setValCol($idColonne, $value);
	}

    /**
     * retourne la liste des colonnes qui déclenchent un update partiel
     * @return array
     */
    public function getLinkedColumns(): array
    {
        if ($this->m_clStructElem instanceof StructureElement){
            return $this->m_clStructElem->getTabColonneAvecOption(StructureColonne::OPTION_Link) ;
        }
        return array();
    }

    /**
     * retourne la liste des colonnes qui correspondent à une option
     * @param $option
     * @return array
     */
    public function getTabColonneAvecOption($option): array
    {
        if ($this->m_clStructElem instanceof StructureElement) {
            return $this->m_clStructElem->getTabColonneAvecOption($option);
        }
        return array();
    }

    /**
     * Tableau clé->valeur pour les colonne/liste élements
     * Appelé dans transformViewRecord2JSON
     * @return array
     */
    public function getTabColonne2IDTableauLie(): array
    {
        // Récupère un tableau associatif [Id Colonne] -> [Id TabLie] pour tout le formulaire
        if ($this->m_clStructElem instanceof StructureElement) {
            return $this->m_clStructElem->getTabColonne2IDTableauLie();
        }
        return array();
    }

    /**
     * enlève toutes les colonnes modifiées
     * @return $this
     */
    public function resetLastModified(): Record
    {
        array_walk($this->m_TabColumnsModified, function(&$item){
            $item=0;
        });
        return $this;
    }

    public function updateRecordLie(RecordCache $src): Record
    {
        $this->m_clCacheRecordLie->update($src);
        return $this;
    }

    /**
     * met à jour l'enregistrement depuis la réponse de NOUTOnline
     * @param Record $clRecordSrc
     * @return $this
     */
    public function updateFromRecord(Record $clRecordSrc): Record
    {
        $this->resetLastModified();

        //mise à jour du titre
        $this->m_sTitle = $clRecordSrc->getTitle();
        $this->m_clCacheRecordLie->update($clRecordSrc->m_clCacheRecordLie);

        //mise à jour des valeurs
        foreach($clRecordSrc->m_TabColumnsValues as $idcolonne=>$value)
        {
            $this->setValCol($idcolonne, $value, false);
        }

        //il faut mettre à jour l'etat des champs
        foreach($clRecordSrc->m_TabColumnsInfo as $idcolonne=>$clInfo)
        {
            $this->m_TabColumnsInfo[$idcolonne]=$clInfo;
        }
        return $this;
    }

    public function emptyPassword()
    {
        foreach($this->m_clStructElem->getMapIDColonne2Structure() as $idcolonne=>$clStructureColonne)
        {
            if (!$clStructureColonne->isOption(StructureColonne::OPTION_Hidden))
            {
                $transform = $clStructureColonne->getOption(StructureColonne::OPTION_Transform);
                if ($transform == StructureColonne::OPTION_Transform_Secret)
                {
                    //il faut vider le champ
                    $this->m_TabColumnsValues[$idcolonne]='';
                }
            }

        }
    }

    /**
     * @param $option
     * @return string
     */
    public function transformOption2CSSProperty($option): string
    {

        $SIMAXStyleToCSS = array(
            "color"         => "color",         // Couleur du texte
            "bgcolor"       => "background-color",    // Couleur de fond
            "bold"          => "font-weight",   // Epaisseur (blod..)
            "italic"        => "font-style",    // Normal, italique..
            "typeElement"   => "text-align"     // Alignement du texte en fonction du type de la colonne
        );

        if (array_key_exists($option, $SIMAXStyleToCSS))
        {
            return $SIMAXStyleToCSS[$option];
        }
        return '';
    }

    /**
     * @param      $option
     * @param null $value
     * @return string|null
     */
    public function transformOptionValue2CSSValue($option, $value=null): ?string
    {
        if (is_null($value))
        {
            $value = $this->getOptionLayout($option);
        }

        // Si c'est une couleur on doit rajouter le #
        // Si c'est un 0 ou un 1 on doit envoyer la bonne valeur..
        // donc ça dépend aussi de l'option

        switch($option)
        {
            case "typeElement":
            {
                switch ($value)
                {
                case StructureColonne::TM_Entier:
                case StructureColonne::TM_Monetaire:
                case StructureColonne::TM_Reel:
                    return "right";
                }
                return null;
            }
            case "bold":
            case "italic":
            {
                if($value == "1")
                {
                    return $option;
                }
                return "";
            }

            case "color":
            case "bgcolor":
                return '#'.$value;
        }

        return null;
    }

    /**
     * @param false      $onlyModified
     * @param array|null $aFilesToSend
     * @return array
     */
    protected function _filterTabColumnsValues(array $aFilesToSend = null, $onlyModified=false) : array
    {
        $aTabColumnsValues = array_filter($this->m_TabColumnsValues, function ($sIDColonne) use ($aFilesToSend, $onlyModified)
        {
            if(!is_null($aFilesToSend) && array_key_exists($sIDColonne, $aFilesToSend)){
                return true;
            }

            return (!$onlyModified || $this->isModified($sIDColonne, true));

        }, ARRAY_FILTER_USE_KEY);

        return $aTabColumnsValues;
    }

    /**
     * @param null|array  $aFilesToSend
     * @param false $onlyModified
     * @return string
     */
    public function getXMLColonne(array $aFilesToSend = null, $onlyModified=false): string
    {
        $aTabColumnsValues = $this->_filterTabColumnsValues($aFilesToSend, $onlyModified);
        $aTabColumnsMultilangue = $this->_aGetColMultilangue();

        return ParametersManagement::s_sStringifyXMLColonne($aTabColumnsValues, $aTabColumnsMultilangue, $aFilesToSend);
    }

    /**
     * @param array $aFilesToSend
     * @return string
     */
    public function getUpdateData(array $aFilesToSend): string
    {
        $sIDForm                    = $this->m_clStructElem->getID();

        $aTabColumnsValues = $this->_filterTabColumnsValues($aFilesToSend, true);
        $aTabColumnsMultilangue = $this->_aGetColMultilangue();

        $sUpdateData = ParametersManagement::s_sStringifyUpdateData($sIDForm, $aTabColumnsValues, $aTabColumnsMultilangue, $aFilesToSend);
        return $sUpdateData;
    }

    /**
     * @return array
     */
    protected function _aGetColMultilangue() : array
    {
        $aRet = $this->m_clStructElem->filterStructureColonne(StructureColonne::OPTION_Modele_Multilanguage);
        array_walk($aRet, function(StructureColonne $clStructureColonne){
            return $clStructureColonne->getIDColonne();
        });
        return $aRet;
    }


    const OPTION_Icon = 'recordIconID';
    const OPTION_RColor = 'recordColor';

    const OPTION_Bold = 'bold';
    const OPTION_Color = 'color';
    const OPTION_BGColor = 'bgcolor';
    const OPTION_Italic = 'italic';
    const OPTION_DisplayMode = 'displayMode';
    const OPTION_DisplayDefault = 'displayDefault';
    const OPTION_Unit = 'unit';
    const OPTION_Filename = 'filename';

    const OPTION_Type = 'type';
    const OPTION_Record = 'record';
    const OPTION_RecorWithChildren = 'recordWithChildren';
}
