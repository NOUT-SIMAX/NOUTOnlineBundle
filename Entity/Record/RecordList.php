<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

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
     * @var boolean $possibleReorder - Indicates wether the user should be able to reorder the recordlist
     */
    protected $possibleReorder;



    public function __construct($sTitle, $sIDAction, $sIDForm, $TabIDEnreg, StructureElement $clStructElem, $possibleReorder)
    {
        $this->m_sTitle = $sTitle;
        $this->m_sIDAction = $sIDAction;
        $this->m_nIDTableau = $sIDForm;
        $this->m_clStructElem = $clStructElem;
        $this->m_TabEnregTableau = $TabIDEnreg;
        $this->possibleReorder = $possibleReorder;
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
        return $this->possibleReorder;
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