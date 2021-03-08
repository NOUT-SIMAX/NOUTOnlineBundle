<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/10/14
 * Time: 11:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Chart;

class Chart
{
	const TYPE_Histogramme       = 'column';
	const TYPE_HistogrammeEmpile = 'stackedColumn';
	const TYPE_Courbe            = 'line';
	const TYPE_CourbeLisse       = 'smoothLine';
	const TYPE_CourbePleine      = 'fullLine';
	const TYPE_Camembert         = 'pie';
	const TYPE_Bulle             = 'bubble';

    const CALCULATION_Somme     = 'Sum';
    const CALCULATION_Compteur  = 'Count';
    const CALCULATION_Max       = 'Max';
    const CALCULATION_Min       = 'Min';
    const CALCULATION_Average   = 'Average';

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
    protected $m_TabAxes=[];

    /**
     * @var ChartAxis[]
     * tableau des axes
     */
    protected $m_TabCalculus=[];

	/**
	 * @var ChartSerie[]
	 * tableau des series
	 */
    protected $m_TabSeries=[];

	public function __construct(string $sTitre, string $sType)
	{
		$this->m_sTitre   = $sTitre;
		$this->m_sType    = $sType;
	}

    /**
     * @return string
     */
	public function getType() : string
    {
        return $this->m_sType;
    }

    /**
     * @return string
     */
    public function getTitle() : string
    {
        return $this->m_sTitre;
    }

    /**
     * @param ChartAxis $clAxis
     */
	public function addAxe(ChartAxis $clAxis)
    {
        $this->m_TabAxes[]=$clAxis;
    }

    /**
     * @return ChartAxis[]
     */
    public function getAxis() : array
    {
        return $this->m_TabAxes;
    }

    /**
     * @param ChartAxis $clAxis
     */
    public function addCalculus(ChartAxis $clAxis)
    {
        $this->m_TabCalculus[]=$clAxis;
    }

    /**
     * @return ChartAxis[]
     */
    public function getCalculus(): array
    {
        return $this->m_TabCalculus;
    }

    /**
     * @param ChartSerie $clSerie
     */
    public function addSerie(ChartSerie $clSerie)
    {
        $this->m_TabSeries[]=$clSerie;
    }

    /**
     * @return ChartSerie[]
     */
    public function getSeries() : array
    {
        return $this->m_TabSeries;
    }

    /**
     * @return int
     */
    public function getNbSeries() : int
    {
        return count($this->m_TabSeries);
    }

}
