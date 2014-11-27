<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:59
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Handler;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class NOUTOnlineLogoutHandler implements LogoutHandlerInterface
{
	/**
	 * @var SOAPProxy
	 */
	private $m_clSOAPProxy;

	/**
	 * @param Router $router
	 * @param SecurityContext $security
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 */
	public function __construct(OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue)
	{
		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
	}

	/**
	 * Invalidate the current session
	 *
	 * @param Request        $request
	 * @param Response       $response
	 * @param TokenInterface $token
	 * @return void
	 */
	public function logout(Request $request, Response $response, TokenInterface $oToken)
	{
		$oUser=$oToken->getUser();
		$TabHeader=array('UsernameToken'=>new UsernameToken($oUser->getUsername(), $oUser->getPassword()), 'SessionToken'=>$oToken->getSessionToken());

		try
		{
			//Disconnect
			$this->m_clSOAPProxy->disconnect($TabHeader);
		}
		catch(\Exception $e)
		{
			//erreur à la connexion
			$clReponseXML = $this->m_clSOAPProxy->getXMLResponseWS();
			if ($clReponseXML instanceof XMLResponseWS)
			{
				if ($clReponseXML->getNumError()!=OnlineError::ERR_UTIL_DECONNECTE)
					throw new \Exception($clReponseXML->getMessError());
			}
			else
			{
				throw new \Exception('Error on logout');
			}
		}

		//$request->getSession()->invalidate();
	}
}