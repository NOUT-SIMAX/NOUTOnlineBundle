<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 16:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Chart\Chart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Chart\ChartAxis;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Chart\ChartSerie;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Chart\ChartTuple;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserChart extends Parser
{
    /** @var Chart */
    protected $m_clChart;

    /**
     * @return Chart
     */
    public function getChart() : Chart
    {
        return $this->m_clChart;
    }

    /**
     * @param XMLResponseWS $clXMLReponseWS
     */
	public function Parse(XMLResponseWS $clXMLReponseWS)
	{
        $ndXML = $clXMLReponseWS->getNodeXML();

		$ndChart = $ndXML->chart;

		$this->m_clChart = new Chart((string) $ndChart->title, (string) $ndChart->chartType);

		foreach ($ndChart->axes->axis as $ndAxis)
		{
			$TabAttributes                    = $ndAxis->attributes(self::NAMESPACE_NOUT_XML);
			$sID                              = (string) $TabAttributes['id'];
			$bCalculation                     = isset($TabAttributes['isCalculation']) ? ((int) $TabAttributes['isCalculation'] != 0) : false;
			$clAxis = new ChartAxis($sID, (string) $TabAttributes['label'], $bCalculation);

			if ($bCalculation){
                $this->m_clChart->addCalculus($clAxis);
            }
			else {
                $this->m_clChart->addAxe($clAxis);
            }
		}

		if (count($ndChart->series))
        {
            //plusieurs series
            foreach($ndChart->series->serie as $ndSerie)
            {
                $TabAttributes = $ndSerie->attributes(self::NAMESPACE_NOUT_XML);
                $clSerie = new ChartSerie( (string) $TabAttributes['label']);

                $this->_parseTuple($clSerie, $ndSerie);
            }

        }
		else
        {
            $clSerie = new ChartSerie();
            $this->_parseTuple($clSerie, $ndChart->serie);
        }

	}

    /**
     * @param ChartSerie        $clSerie
     * @param \SimpleXMLElement $ndSerie
     */
	protected function _parseTuple(ChartSerie $clSerie, \SimpleXMLElement $ndSerie)
    {
        foreach ($ndSerie->tuple as $ndTuple)
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
            $clSerie->addTuple($clTuple);
        }
        $this->m_clChart->addSerie($clSerie);
    }
}