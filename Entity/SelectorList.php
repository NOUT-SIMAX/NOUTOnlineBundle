<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/2016
 * Time: 14:31
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;

class SelectorList
{
    /**
     * @var RecordList
     */
    protected $m_clList;

    /**
     * @var string
     */
    protected $m_sTitle;

    /**
     * SelectorList constructor.
     * @param RecordList $list
     */
    public function __construct(RecordList $list)
    {
        $this->m_clList = $list;
        $this->m_sTitle = $list->getTitle();
    }

    /**
     * @return RecordList
     */
    public function getList(): RecordList
    {
        return $this->m_clList;
    }

    /**
     * @param RecordList $clList
     */
    public function setList(RecordList $clList)
    {
        $this->m_clList = $clList;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->m_sTitle;
    }

    /**
     * @param string $sTitle
     */
    public function setTitle(string $sTitle)
    {
        $this->m_sTitle = $sTitle;
    }



}