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
	public $m_sTitre;

	/**
	 * @var string
	 */
	public $m_sType;

	/**
	 * @var array
	 * tableau des axes
	 */
	public $m_TabAxes;

	/**
	 * @var array
	 * tableau des series
	 */
	public $m_TabSeries;

	public function __construct()
	{
		$this->m_sTitre   = '';
		$this->m_sType    = '';
		$this->m_TabAxes  = array();
		$this->m_TabSeries = array();
	}
}
