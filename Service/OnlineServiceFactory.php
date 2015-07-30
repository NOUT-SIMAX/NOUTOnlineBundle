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
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;

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
		$this->m_clCache  = $cache;
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return SOAPProxy
	 */
	public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration, $sIP)
	{
		if (empty(trim($sIP)))
		{
			throw new \Exception('L\'addresse IP du client ne doit pas être vide');
		}

		$OnlineService = new SOAPProxy($clConfiguration, $this->m_clLogger, $this->m_clCache);
		$OnlineService->setIPClient($sIP);

		return $OnlineService;
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return RESTProxy
	 */
	public function clGetRESTProxy(ConfigurationDialogue $clConfiguration, $sIP)
	{
		if (empty(trim($sIP)))
		{
			throw new \Exception('L\'addresse IP du client ne doit pas être vide');
		}

		$OnlineService = new RESTProxy($clConfiguration, $this->m_clLogger);
		$OnlineService->setIPClient($sIP);

		return $OnlineService;
	}
}
