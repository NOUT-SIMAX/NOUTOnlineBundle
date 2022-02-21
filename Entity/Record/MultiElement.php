<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;



use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Count;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ListSort;

class MultiElement extends IHMWindows
{
    /** @var string $m_sIDAction : identifiant de l'action */
    protected $m_sIDAction='';

    /** @var Record $m_clRecordParam : contient l'enregistrement qui correspond à la fiche */
    protected $m_clRecordParam=null;

    /** @var array tableau qui contient les types d'affichage possible */
    protected $m_TabPossibleDisplayMode = array();

    /** @var string mode d'affichage par défaut */
    protected $m_eDefaultDisplayMode='';

    /** @var string type de graphe par defauts  */
    protected $m_eDefaultGraphType='';


    /** @var Count|null */
    protected $m_clCount = null;

    /** @var boolean $m_bPossibleReorder - Indicates wether the user should be able to reorder the recordlist */
    protected $m_bPossibleReorder=false;

    /** @var bool */
    protected $m_bActiveReorder=false;

    /** @var array */
    protected $m_TabExports;

    /** @var array */
    protected $m_TabImports;

    /** @var ListSort[] tableau des tris appliqués à la liste */
    protected $m_TabSort;

    /** @var bool  */
    protected $m_bWithGhost=false;


    /**
     * MultiElement constructor.
     * @param string                $sTitle
     * @param string                $sIDAction
     * @param string                $sIDForm
     * @param StructureElement|null $clStructElem
     * @param bool                  $withGhost
     * @param bool                  $possibleReorder
     * @param bool                  $activeReorder
     * @param array                 $exports
     * @param array                 $imports
     * @param array                 $tabSort
     */
    public function __construct(string $sTitle,
                                string $sIDAction,
                                string $sIDForm,
                                StructureElement $clStructElem=null,
                                bool $withGhost=false,
                                bool $possibleReorder=false,
                                bool $activeReorder=false,
                                array $exports=array(),
                                array $imports=array(),
                                $tabSort=array())
    {
        parent::__construct($sTitle, $sIDForm, $clStructElem);

        $this->m_sIDAction = $sIDAction;
        $this->m_bWithGhost = $withGhost;
        $this->m_bPossibleReorder = $possibleReorder;
        $this->m_bActiveReorder = $activeReorder;
        $this->m_TabExports = $exports;
        $this->m_TabImports = $imports;
        $this->m_TabSort = $tabSort;
    }

    /**
     * @param Record|null $clRecordParam
     * @return $this
     */
    public function setParam(Record $clRecordParam=null): MultiElement
    {
        $this->m_clRecordParam = $clRecordParam;
        return $this;
    }

    /**
     * @return Record
     */
    public function getParameters(): ?Record
    {
        return $this->m_clRecordParam;
    }

    /**
     * @return string
     */
    public function getIDAction(): string
    {
        return $this->m_sIDAction;
    }

    /**
     * @return bool
     */
    public function withGhost() : bool
    {
        return $this->m_bWithGhost;
    }

    /**
     * @return string
     */
    public function getDefaultDisplayMode(): string
    {
        return $this->m_eDefaultDisplayMode;
    }

    /**
     * @param string|null $eDefaultDisplayMode
     * @return $this
     */
    public function setDefaultDisplayMode(?string $eDefaultDisplayMode): MultiElement
    {
        $this->m_eDefaultDisplayMode = $eDefaultDisplayMode ?? '';
        return $this;
    }

    /**
     * @param array|null $tabPossibleDisplayMode
     * @return $this
     */
    public function setTabPossibleDisplayMode(array $tabPossibleDisplayMode=null): MultiElement
    {
        $this->m_TabPossibleDisplayMode = $tabPossibleDisplayMode ?? [];
        return $this;
    }

    /**
     * @param $eDisplayMode
     * @return $this
     */
    public function addPossibleDisplayMode($eDisplayMode): MultiElement
    {
        $this->m_TabPossibleDisplayMode[]=$eDisplayMode;
        return $this;
    }


    /**
     * @return array
     */
    public function getTabPossibleDisplayMode(): array
    {
        return $this->m_TabPossibleDisplayMode;
    }

    /**
     * @param string|null $defaultGraphType
     * @return $this
     */
    public function setDefaultGraphType(?string $defaultGraphType) :  MultiElement
    {
        $this->m_eDefaultGraphType = $defaultGraphType ?? '';
        return $this;
    }

    /**
     * @return string
     */
    public function getDefaultGraphType() : string
    {
        return $this->m_eDefaultGraphType;
    }

    /**
     * @param Count|null $count
     */
    public function setCount(Count $count=null)
    {
        $this->m_clCount = $count;
    }

    /**
     * @return Count|null
     */
    public function getCount(): ?Count
    {
        return $this->m_clCount;
    }

    public function hasPossibleReorder(): bool
    {
        return $this->m_bPossibleReorder;
    }

    public function hasActiveReorder(): bool
    {
        return $this->m_bActiveReorder;
    }

    /**
     * @return array
     */
    public function getExports(): array
    {
        return $this->m_TabExports;
    }

    /**
     * @return array
     */
    public function getImports(): array
    {
        return $this->m_TabImports;
    }

    /**
     * @return ListSort[]
     */
    public function getTabSort(): array
    {
        return $this->m_TabSort;
    }

}