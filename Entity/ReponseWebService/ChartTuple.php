<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/10/14
 * Time: 14:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class ChartTuple
{
	public $m_TabID2DataLabel;

	public function __construct()
	{
		$this->m_TabID2DataLabel=array();
	}

	public function Add($sID, $Data, $sDisplay)
	{
		$clElement = new \stdClass();
		$clElement->m_Data = $Data;
		$clElement->m_sDisplayValue = $sDisplay;
		$clElement->m_sID = $sID;

		$this->m_TabID2DataLabel[$sID]=$clElement;
	}
} 