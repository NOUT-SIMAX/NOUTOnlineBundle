<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Count;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ListSort;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\RecordCache;

/**
 * Class RecordList, Description d'une liste d'enregistrement
 *
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class RecordList 
{
    /**
     * @var string $m_sTitle : contient la mini desc de l'action qui a retournée une liste
     */
    protected $m_sTitle;

    /**
     * @var string $m_sIDAction : identifiant de l'action
     */
    protected $m_sIDAction;

    /**
     * @var Record $m_clRecordParam : contient l'enregistrement qui correspond à la fiche
     */
    protected $m_clRecordParam;

    /**
     * @var RecordCache $m_clRecordCache : Contient les données de la fiche
     */
    protected $m_clRecordCache;

    /**
     * @var string $m_nIDTableau : identifiant du formulaire
     */
    protected $m_nIDTableau;

    /**
     * @var StructureElement
     */
    protected $m_clStructElem;

    /**
     * @var EnregTableauArray;
     * tableau qui contient les identifiants des enregistrements avec conservation de l'ordre de la réponse
     */
    protected $m_TabEnregTableau;

    /**
     * @var array
     * tableau qui contient les types d'affichage possible
     */
    protected $m_TabPossibleDisplayMode = array();

    /**
     * @var string
     * tableau qui contient les types d'affichage possible
     */
    protected $m_eDefaultDisplayMode;

    /**
     * @var boolean $m_bPossibleReorder - Indicates wether the user should be able to reorder the recordlist
     */
    protected $m_bPossibleReorder;

    /**
     * @var bool
     */
    protected $m_bActiveReorder;

    /**
     * @var
     */
    protected $m_TabExports;

    /**
     * @var
     */
    protected $m_TabImports;


    /**
     * @var ListSort[]
     * tableau des tris appliqués à la liste
     */
    protected $m_TabSort;

    /** @var Count|null */
    protected $m_clCount = null;

    /**
     * RecordList constructor.
     * @param $sTitle
     * @param $sIDAction
     * @param $sIDForm
     * @param $TabIDEnreg
     * @param $clStructElem
     * @param $possibleReorder
     * @param $activeReorder
     * @param $exports
     * @param $imports
     * @param $tabSort
     */
    public function __construct($sTitle, $sIDAction, $sIDForm, $TabIDEnreg, $clStructElem, $possibleReorder, $activeReorder, $exports, $imports, $tabSort=array())
    {
        $this->m_sTitle = $sTitle;
        $this->m_sIDAction = $sIDAction;
        $this->m_nIDTableau = $sIDForm;
        $this->m_clStructElem = $clStructElem;
        $this->m_TabEnregTableau = $TabIDEnreg;
        $this->m_bPossibleReorder = $possibleReorder;
        $this->m_bActiveReorder = $activeReorder;
        $this->m_TabExports = $exports;
        $this->m_TabImports = $imports;
        $this->m_TabSort = $tabSort;
    }

    /**
     * @param Record $clRecordParam
     * @return $this
     */
    public function setParam(Record $clRecordParam=null)
    {
        $this->m_clRecordParam = $clRecordParam;
        return $this;
    }

    /**
     * @param $clRecordCache
     * @return $this
     */
    public function setRecordCache($clRecordCache=null)
    {
        $this->m_clRecordCache = $clRecordCache;
        return $this;
    }

    /**
     * @return Record
     */
    public function getParameters()
    {
        return $this->m_clRecordParam;
    }

    /**
     * @return string
     */
    public function getIDAction()
    {
        return $this->m_sIDAction;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->m_sTitle;
    }

    /**
     * @return StructureElement
     */
    public function getStructElem()
    {
        return $this->m_clStructElem;
    }

    /**
     * @return RecordCache
     */
    public function getRecordCache()
    {
        return $this->m_clRecordCache;
    }

    /**
     * @return EnregTableauArray
     */
    public function getTabIDEnreg()
    {
        return $this->m_TabEnregTableau;
    }

    /**
     * @return string
     */
    public function getIDTableau()
    {
        return $this->m_nIDTableau;
    }

    /**
     * @return array
     */
    public function getTabPossibleDisplayMode()
    {
        return $this->m_TabPossibleDisplayMode;
    }

    /**
     * @return array
     */
    public function getExports() {
        return $this->m_TabExports;
    }

    /**
     * @return array
     */
    public function getImports() {
        return $this->m_TabImports;
    }

    /**
     * @return ListSort[]
     */
    public function getTabSort()
    {
        return $this->m_TabSort;
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
    public function getCount()
    {
        return $this->m_clCount;
    }

    /**
     * @param array $tabPossibleDisplayMode
     * @return $this
     */
    public function setTabPossibleDisplayMode($tabPossibleDisplayMode)
    {
        $this->m_TabPossibleDisplayMode = $tabPossibleDisplayMode;
        return $this;
    }

    /**
     * @param $eDisplayMode
     * @return $this
     */
    public function addPossibleDisplayMode($eDisplayMode)
    {
        $this->m_TabPossibleDisplayMode[]=$eDisplayMode;
        return $this;
    }

    public function hasPossibleReorder() {
        return $this->m_bPossibleReorder;
    }

    public function hasActiveReorder() {
        return $this->m_bActiveReorder;
    }

    /**
     * @return string
     */
    public function getDefaultDisplayMode()
    {
        return $this->m_eDefaultDisplayMode;
    }

    /**
     * @param string $eDefaultDisplayMode
     * @return $this
     */
    public function setDefaultDisplayMode($eDefaultDisplayMode)
    {
        $this->m_eDefaultDisplayMode = $eDefaultDisplayMode;
        return $this;
    }

}