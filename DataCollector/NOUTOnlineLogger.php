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
class NOUTOnlineLogger {

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
		$this->m_bEnabled = $debug;
	}

	public function startQuery()
	{
		if ($this->m_bEnabled) {
			$this->m_fStart = microtime(true);
		}
	}

	public function startSend()
	{
		if ($this->m_bEnabled) {
			$this->m_fSend = microtime(true);
		}
	}

	public function stopSend()
	{
		if ($this->m_bEnabled) {
			$this->m_fSend = microtime(true) - $this->m_fSend;
		}
	}

	/**
	 * @param $sTo
	 * @param $sFrom
	 */
	public function stopQuery($sTo, $sFrom, $sOperation, $bSOAP, $bXML=false)
	{
		if ($this->m_bEnabled) {

			$this->m_clMonolog->debug($sTo);
			$this->m_clMonolog->debug($sFrom);

			$this->m_TabQueries[] = array(
				'request' => $sTo,
				'response'=> $sFrom,
				'executionMS' => microtime(true) - $this->m_fStart,
				'sendMS' => $this->m_fSend,
				'operation'=>$sOperation,
				'soap'=>$bSOAP,
				'xml'=> $bSOAP ? true : $bXML,
			);
		}
	}
} 