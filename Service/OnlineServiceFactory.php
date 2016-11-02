<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
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
	 * @var \NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider
	 */
	protected $m_clCache;

	/**
	 * @var ClientInformation
	 */
	protected $m_clClientInformation;


	public function __construct(ClientInformation $clientInfo, NOUTOnlineLogger $logger, $cache_dir)
	{
		$this->m_clLogger = $logger;
		$this->m_clClientInformation=$clientInfo;

        $this->m_clCache = NOUTCacheProvider::initCache('noutonline', null, $cache_dir);
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return SOAPProxy
	 */
	public function clGetSOAPProxy(ConfigurationDialogue $clConfiguration)
	{
        try {
            $OnlineService = new SOAPProxy($this->m_clClientInformation, $clConfiguration, $this->m_clLogger, $this->m_clCache);
            return $OnlineService;
        }
        catch(\Exception $e){
            return null;
        }
	}

	/**
	 * @param ConfigurationDialogue $clConfiguration
	 * @return RESTProxy
	 */
	public function clGetRESTProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new RESTProxy($this->m_clClientInformation, $clConfiguration, $this->m_clLogger);
		return $OnlineService;
	}
}
