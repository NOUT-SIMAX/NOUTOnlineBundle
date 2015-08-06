<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


class ParserChart extends Parser
{


	public function Parse(\SimpleXMLElement $ndXML)
	{
		$ndChart = $ndXML->chart;

		$this->m_clChart = new Chart();
		$this->m_clChart->m_sTitre = (string) $ndChart->title;
		$this->m_clChart->m_sType  = (string) $ndChart->chartType;

		foreach ($ndChart->axes->axis as $ndAxis)
		{
			$TabAttributes                    = $ndAxis->attributes(self::NAMESPACE_NOUT_XML);
			$sID                              = (string) $TabAttributes['id'];
			$bCalculation                     = isset($TabAttributes['isCalculation']) ? ((int) $TabAttributes['isCalculation'] != 0) : false;
			$this->m_clChart->m_TabAxes[$sID] = new ChartAxis($sID, (string) $TabAttributes['label'], $bCalculation);
		}

		foreach ($ndChart->serie->tuple as $ndTuple)
		{
			$clTuple = new ChartTuple();
			foreach ($ndTuple->children() as $ndFils)
			{
				$sTagName = $ndFils->getName();
				if (strncmp($sTagName, 'id_', strlen('id_')) == 0)
				{
					$sID = str_replace('id_', '', $sTagName);
					$clTuple->Add($sID, (string) $ndFils->data, (string) $ndFils->displayValue);
				}
			}
			$this->m_clChart->m_TabSeries[] = $clTuple;
		}
	}
}