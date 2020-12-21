<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class ChartSerie
{
    /**
     * @var ChartTuple[]
     */
    protected $m_TabTuples=[];

    /**
     * @var string
     */
    protected $m_sTitle = '';

    public function __construct(string $sTitle='')
    {
        $this->m_sTitle = $sTitle;
    }

    /**
     * @param ChartTuple $tuple
     */
    public function addTuple(ChartTuple $tuple)
    {
        $this->m_TabTuples[]=$tuple;
    }

    /**
     * @return ChartTuple[]
     */
    public function getTuples() : array
    {
        return $this->m_TabTuples;
    }
}