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

	public function clGetServiceProxy()
	{
		$sEndPoint = './bundles/noutonline/Service.wsdl';
		$sService = 'http://127.0.0.1:8062';

		//on récupére le prefixe (http | https);
		$sProtocolPrefix = substr($sService,0,strpos($sService,'//')+2 );

		list($sHost,$sPort) = explode(':', str_replace($sProtocolPrefix,'',$sService) );

		return new OnlineServiceProxy(new ConfigurationDialogue($sEndPoint, true, $sHost, $sPort,$sProtocolPrefix));
	}
} 