<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 25/07/14
 * Time: 10:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\DataCollector;

/**
 * Class NOUTOnlineLogger
 * @package NOUT\Bundle\NOUTOnlineBundle\DataCollector
 */
class NOUTOnlineLogger
{
	/**
	 * Executed NOUTOnline queries.
	 *
	 * @var array
	 */
	public $m_TabQueries = array();

	/**
	 * If NOUTOnline Logger is enabled (log queries) or not.
	 *
	 * @var boolean
	 */
	public $m_bEnabled = true;

	/**
	 * @var float|null
	 */
	public $m_fStart = null;

	/**
	 * tableau qui contient les temps intermédiaire
	 * @var array|null
	 */
	public $m_fSend = null;

	/**
	 * @var $m_clMonolog : monolog pour voir les traces
	 */
	protected $m_clMonolog;

	/**
	 * @param $logger : l'instance monolog de symfony
	 * @param $debug : si site en debug
	 */
	public function __construct($logger, $debug)
	{
		$this->m_clMonolog = $logger;
		$this->m_bEnabled  = $debug;
	}

	public function startQuery()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fStart = microtime(true);
		}
	}

	public function startSend()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fSend = microtime(true);
		}
	}

	public function stopSend()
	{
		if ($this->m_bEnabled)
		{
			$this->m_fSend = microtime(true) - $this->m_fSend;
		}
	}

	/**
	 * @param $sTo
	 * @param $sFrom
	 */
	public function stopQuery($sTo, $sFrom, $sOperation, $bSOAP, $bXML = false)
	{
		if ($this->m_bEnabled)
		{
			if ($bSOAP)
			{
				$sTo = str_replace('><', ">\r\n<", $sTo);
			}

			$this->m_clMonolog->debug($sTo);
			$this->m_clMonolog->debug($sFrom);

			if ($bSOAP)
			{
				$sSeparateur = "\r\n\r\n";
				$nPosT       = strpos($sTo, $sSeparateur);
				$nPosF       = strpos($sFrom, $sSeparateur);

				if ($nPosT)
				{
					$sRequestHeader = substr($sTo, 0, $nPosT);
					$sRequest       = substr($sTo, $nPosT+strlen($sSeparateur));
				}
				else
				{
					$sRequestHeader = '';
					$sRequest       = $sTo;
				}

				if ($nPosF)
				{
					$sResponseHeader = substr($sFrom, 0, $nPosF);
					$sResponse       = substr($sFrom, $nPosF+strlen($sSeparateur));
				}
				else
				{
					$sResponseHeader = '';
					$sResponse       = $sFrom;
				}
			}
			else
			{
				$sRequest  = $sTo;
				$sResponse = $sFrom;
			}

			$this->m_TabQueries[] = array(
				'request'         => $sRequest,
				'response'        => $sResponse,
				'request_header'  => $bSOAP ? $sRequestHeader : '',
				'response_header' => $bSOAP ? $sResponseHeader : '',
				'executionMS'     => microtime(true) - $this->m_fStart,
				'sendMS'          => $this->m_fSend,
				'operation'       => $sOperation,
				'soap'            => $bSOAP,
				'xml'             => $bSOAP ? true : $bXML,
			);
		}
	}
}
