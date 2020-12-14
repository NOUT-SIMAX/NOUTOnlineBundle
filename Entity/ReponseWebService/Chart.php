<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/10/14
 * Time: 11:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class Chart
{
	const TYPE_Histogramme       = 'column';
	const TYPE_HistogrammeEmpile = 'stackedColumn';
	const TYPE_Courbe            = 'line';
	const TYPE_CourbeLissee      = 'line';
	const TYPE_Camembert         = 'pie';
	const TYPE_Bulle             = 'bubble';

	/**
	 * @var string
	 */
	protected $m_sTitre;

	/**
	 * @var string
	 */
    protected $m_sType;

	/**
	 * @var ChartAxis[]
	 * tableau des axes
	 */
    protected $m_TabAxes;

	/**
	 * @var ChartTuple[]
	 * tableau des series
	 */
    protected $m_TabSeries;

	public function __construct(string $sTitre, string $sType)
	{
		$this->m_sTitre   = $sTitre;
		$this->m_sType    = $sType;
		$this->m_TabAxes  = [];
		$this->m_TabSeries = [];
	}

	public function getType()
    {
        return $this->m_sType;
    }

    public function getTitle()
    {
        return $this->m_sTitre;
    }

    /**
     * @param ChartAxis $clAxis
     */
	public function addAxe(ChartAxis $clAxis)
    {
        $this->m_TabAxes[$clAxis->getID()]=$clAxis;
    }

    /**
     * @return ChartAxis|null
     */
    public function getXAxis() : ?ChartAxis
    {
        foreach($this->m_TabAxes as $clAxis)
        {
            if (!$clAxis->isCalculation()){
                return $clAxis;
            }
        }
        return null;
    }

    /**
     * @return array
     */
    public function getYAxis(): array
    {
        $ret = array();
        foreach($this->m_TabAxes as $clAxis)
        {
            if ($clAxis->isCalculation()){
                $ret[]=$clAxis;
            }
        }
        return $ret;
    }

    /**
     * @return ChartAxis[]
     */
    public function getAxes(): array
    {
        return $this->m_TabAxes;
    }

    /**
     * @param ChartTuple $clSerie
     */
    public function addSerie(ChartTuple $clSerie)
    {
        $this->m_TabSeries[]=$clSerie;
    }

    /**
     * @return ChartTuple[]
     */
    public function getSeries()
    {
        return $this->m_TabSeries;
    }

}
