<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\GestionWSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class OnlineServiceFactory
{
    /**
     * @var int
     */
    protected $nSoapSocketTimeout = -1;
    /**
     * @var NOUTOnlineLogger
     */
    protected NOUTOnlineLogger $clLogger;

    /**
     * @var ClientInformation
     */
    protected ClientInformation $clClientInformation;

    /**
     * @var Stopwatch
     */
    protected ?Stopwatch $clStopwatch;

    /** @var */
    protected GestionWSDL $clGestionWSDL;

    protected TokenStorageInterface $clTokenStorage;

    public function __construct(ClientInformation          $clientInfo,
                                NOUTOnlineLogger           $logger,
                                GestionWSDL                $clGestionWSDL,
                                DynamicConfigurationLoader $configLoader,
                                TokenStorageInterface      $tokenStorage,
                                Stopwatch                  $stopwatch = null)
    {
        $this->nSoapSocketTimeout = $configLoader->getParameter('soap_socket_timeout', -1);
        $this->clLogger = $logger;
        $this->clStopwatch = $stopwatch;

        $this->clClientInformation = $clientInfo;
        $this->clGestionWSDL = $clGestionWSDL;
        $this->clTokenStorage = $tokenStorage;
    }

    /**
     * @param ConfigurationDialogue $clConfiguration
     * @return SOAPProxy
     */
    public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration)
    {
        // Peut renvoyer une exception si pb de connexion
        return new SOAPProxy(
            $this->clClientInformation,
            $clConfiguration,
            $this->clLogger,
            $this->clGestionWSDL,
            $this->clTokenStorage,
            $this->clStopwatch,
            $this->nSoapSocketTimeout
        );
    }

    /**
     * @param ConfigurationDialogue $clConfiguration
     * @return RESTProxy
     */
    public function clGetRESTProxy(ConfigurationDialogue $clConfiguration)
    {
        $clCurl = new CURLProxy($this->clClientInformation, $this->clLogger, $clConfiguration, $this->clStopwatch);
        return new RESTProxy($clConfiguration, $clCurl, $this->clTokenStorage);
    }
}
