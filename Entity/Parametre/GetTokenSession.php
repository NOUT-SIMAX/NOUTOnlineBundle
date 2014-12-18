<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 10:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession as WSDLGetTokenSession;

class GetTokenSession extends WSDLGetTokenSession
{
	public function __construct()
	{
		$this->UsernameToken             = null; // UsernameTokenType
		$this->ExtranetUser              = null; // ExtranetUserType
		$this->DefaultClientLanguageCode = 12; // DefaultClientLanguageCodeType
	}
}
