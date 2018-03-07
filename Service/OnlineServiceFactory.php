<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use Symfony\Component\Stopwatch\Stopwatch;

class OnlineServiceFactory
{
    /**
     * @var int
     */
    protected $__soap_socket_timeout = -1;
	/**
	 * @var \NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger
	 */
	protected $m_clLogger;

	/**
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider
	 */
	protected $m_clCache;

	/**
	 * @var ClientInformation
	 */
	protected $m_clClientInformation;

    /**
     * @var Stopwatch
     */
	protected $__stopwatch;


	public function __construct(ClientInformation $clientInfo,
                                NOUTOnlineLogger $logger,
                                NOUTCacheFactory $cacheFactory,
                                Stopwatch $stopwatch=null,
                                $soap_socket_timeout=SOAPProxy::SOCKET_TIMEOUT)
	{
	    $this->__soap_socket_timeout = $soap_socket_timeout;
		$this->m_clLogger = $logger;
		$this->__stopwatch = $stopwatch;

		$this->m_clClientInformation=$clientInfo;
        $this->m_clCache = $cacheFactory->getCache('noutonline', '', '');
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return SOAPProxy
	 */
	public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration)
	{

		$OnlineService = new SOAPProxy($this->m_clClientInformation,
                                       $clConfiguration,
                                       $this->m_clLogger,
                                       $this->__stopwatch,
                                       $this->m_clCache,
                                       $this->__soap_socket_timeout);
		return $OnlineService; // Peut renvoyer une exception si pb de connexion

	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return RESTProxy
	 */
	public function clGetRESTProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new RESTProxy($this->m_clClientInformation, $clConfiguration, $this->m_clLogger, $this->__stopwatch);
		return $OnlineService;
	}
}
