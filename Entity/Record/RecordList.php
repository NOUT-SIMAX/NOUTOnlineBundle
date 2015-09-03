<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


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
     * @var string $m_nIDTableau : identifiant du formulaire
     */
    protected $m_nIDTableau;

    /**
     * @var StructureElement
     */
    protected $m_clStructElem;

    /**
     * @var array;
     * tableau qui contient l'ordre des enregistrements avec conservation de l'ordre de la réponse
     */
    protected $m_TabEnregTableau;


    public function __construct($sTitle, $sIDAction, $sIDForm, $TabIDEnreg, StructureElement $clStructElem=null)
    {
        $this->m_sTitle = $sTitle;
        $this->m_sIDAction = $sIDAction;
        $this->m_nIDTableau = $sIDForm;
        $this->m_clStructElem = $clStructElem;
        $this->m_TabEnregTableau = $TabIDEnreg;
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
}