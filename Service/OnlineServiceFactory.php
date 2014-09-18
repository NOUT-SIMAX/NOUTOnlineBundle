<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache;
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;

class OnlineServiceFactory
{
	/**
	 * @var \NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger
	 */
	protected $m_clLogger;

	/**
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache
	 */
	protected $m_clCache;


	public function __construct(NOUTOnlineLogger $logger, NOUTCache $cache)
	{
		$this->m_clLogger = $logger;
		$this->m_clCache = $cache;
	}



	public function clGetServiceProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new OnlineServiceProxy($clConfiguration, $this->m_clLogger, $this->m_clCache);
		return $OnlineService;
	}
} 