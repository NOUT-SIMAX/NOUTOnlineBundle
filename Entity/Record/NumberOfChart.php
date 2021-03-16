<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class NumberOfChart extends MultiElement
{
    /** @var int  */
    protected $m_nNbChart=0;

    /**
     * @return int
     */
    public function getNbChart(): int
    {
        return $this->m_nNbChart;
    }

    /**
     * @param int $nNbChart
     * @return NumberOfChart
     */
    public function setNbChart(int $nNbChart): NumberOfChart
    {
        $this->m_nNbChart = $nNbChart;
        return $this;
    }
}