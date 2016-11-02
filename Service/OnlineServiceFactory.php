<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTApcuCache;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTFileCache;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTXCacheCache;
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


        if (extension_loaded('apc') || extension_loaded('apcu'))
        {
            $this->m_clCache = new NOUTApcuCache();
            $this->m_clCache->setNamespace('noutonline', null);
        }
        elseif (extension_loaded('xcache'))
        {
            $this->m_clCache = new NOUTXCacheCache();
            $this->m_clCache->setNamespace('noutonline', null);
        }
        else
        {
            $this->m_clCache = new NOUTFileCache();
            $this->m_clCache->setNamespace('noutonline', $cache_dir);
        }
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
