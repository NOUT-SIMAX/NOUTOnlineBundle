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
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;

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

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return SOAPProxy
	 */
	public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new SOAPProxy($clConfiguration, $this->m_clLogger, $this->m_clCache);
		return $OnlineService;
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return RESTProxy
	 */
	public function clGetRESTProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new RESTProxy($clConfiguration, $this->m_clLogger);
		return $OnlineService;
	}
} 