<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/2015
 * Time: 09:48
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use Symfony\Component\HttpFoundation\RequestStack;

class ClientInformation
{
	/**
	 * @var RequestStack
	 */
	private $__requestStack;

	/**
	 * @param RequestStack $requestStack
	 */
	public function __construct(RequestStack $requestStack)
	{
		$this->__requestStack=$requestStack;
	}

	public function getIP()
	{
		$oRequest = $this->__requestStack->getCurrentRequest();
		if (is_null($oRequest))
			return '';

		return trim($oRequest->getClientIp());
	}

}