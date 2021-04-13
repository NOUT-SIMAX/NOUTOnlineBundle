<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 10:06
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExtranetUserType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession as WSDLGetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UsernameToken;

class GetTokenSession extends WSDLGetTokenSession
{
    /**
     * Constructor method for GetTokenSession
     * @param string|null $token
     * @param UsernameToken|null $usernameToken
     * @param ExtranetUserType|null $extranetUser
     * @param int $defaultClientLanguageCode
     */
    public function __construct(string $token = null, UsernameToken $usernameToken = null, ExtranetUserType $extranetUser = null, int $defaultClientLanguageCode = 12)
    {
        parent::__construct($token, $usernameToken, $extranetUser, $defaultClientLanguageCode);
    }
}
