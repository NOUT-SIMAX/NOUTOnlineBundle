<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;

class OnlineServiceFactory
{
	protected $m_clLogger;
	public function __construct($logger)
	{
		$this->m_clLogger = $logger;
	}



	public function clGetServiceProxy(ConfigurationDialogue $clConfiguration)
	{
		$OnlineService = new OnlineServiceProxy($clConfiguration);
		$OnlineService->setLogger($this->m_clLogger);
		return $OnlineService;
	}
} 