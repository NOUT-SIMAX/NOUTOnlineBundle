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
	protected $m_TabID2DataLabel;

	public function __construct()
	{
		$this->m_TabID2DataLabel = array();
	}

    /**
     * @param string $sID
     * @param        $Data
     * @param string $sDisplay
     */
	public function Add(string $sID, $Data, string $sDisplay)
	{
		$clElement                  = new \stdClass();
		$clElement->m_Data          = $Data;
		$clElement->m_sDisplayValue = $sDisplay;
		$clElement->m_sID           = $sID;

		$this->m_TabID2DataLabel[$sID] = $clElement;
	}

    /**
     * @param string $sID
     * @return null
     */
	public function getData(string $sID)
    {
        if (array_key_exists($sID, $this->m_TabID2DataLabel)){
            return $this->m_TabID2DataLabel[$sID]->m_Data;
        }
        return null;
    }

    /**
     * @param string $sID
     * @return string|null
     */
    public function getDisplay(string $sID) : ?string
    {
        if (array_key_exists($sID, $this->m_TabID2DataLabel)) {
            return $this->m_TabID2DataLabel[$sID]->m_sDisplayValue;
        }
        return null;
    }
}
