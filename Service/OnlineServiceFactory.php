<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\GestionWSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use Symfony\Component\Stopwatch\Stopwatch;

class OnlineServiceFactory
{
    /**
     * @var int
     */
    protected $__soap_socket_timeout = -1;
	/**
	 * @var NOUTOnlineLogger
	 */
	protected $m_clLogger;

	/**
	 * @var ClientInformation
	 */
	protected $m_clClientInformation;

    /**
     * @var Stopwatch
     */
	protected $__stopwatch;

	/** @var  */
	protected $__clGestionWSDL;


	public function __construct(ClientInformation $clientInfo,
                                NOUTOnlineLogger $logger,
                                GestionWSDL $clGestionWSDL,
                                DynamicConfigurationLoader $configLoader,
                                Stopwatch $stopwatch=null)
	{
	    $this->__soap_socket_timeout = $configLoader->getParameter('soap_socket_timeout', -1);
		$this->m_clLogger = $logger;
		$this->__stopwatch = $stopwatch;

		$this->m_clClientInformation=$clientInfo;
        $this->__clGestionWSDL = $clGestionWSDL;
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return SOAPProxy
	 */
	public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration)
	{
        // Peut renvoyer une exception si pb de connexion
        return new SOAPProxy(
            $this->m_clClientInformation,
            $clConfiguration,
            $this->m_clLogger,
            $this->__clGestionWSDL,
            $this->__stopwatch,
            $this->__soap_socket_timeout
        );
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return RESTProxy
	 */
	public function clGetRESTProxy(ConfigurationDialogue $clConfiguration)
	{
		return new RESTProxy($this->m_clClientInformation, $clConfiguration, $this->m_clLogger, $this->__stopwatch);
	}
}
