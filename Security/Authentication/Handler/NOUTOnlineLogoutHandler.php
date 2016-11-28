<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:59
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Authentication\Handler;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;

class NOUTOnlineLogoutHandler implements LogoutHandlerInterface
{
	/**
	 * @var SOAPProxy
	 */
	private $m_clSOAPProxy;

    /**
     * @var EventDispatcherInterface
     */
    private $__eventDispatcher;

    /**
     * @var ConfigurationDialogue
     */
    private $m_clConfigurationDialogue;

	/**
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
     * @param EventDispatcherInterface $eventDispatcher
	 */
	public function __construct(OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue, EventDispatcherInterface $eventDispatcher)
	{
        $this->m_clConfigurationDialogue = $configurationDialogue;
        $this->__eventDispatcher = $eventDispatcher;

        try{
            $this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
        }
        catch(\Exception $e){
            $this->m_clSOAPProxy = null;
            $this->last_exception = $e;
        }

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
        /** @var NOUTToken $oToken */
        $oUsernameToken = new UsernameToken(
            $oToken->getLoginSIMAX(),
            $oToken->getPasswordSIMAX(),
            $this->m_clConfigurationDialogue->getModeAuth(),
            $this->m_clConfigurationDialogue->getSecret()
        );

        if (isset($this->m_clSOAPProxy) && !is_null($this->m_clSOAPProxy))
        {
            $TabHeader=array(
                'UsernameToken'=>$this->m_clSOAPProxy->getUsernameTokenForWdsl($oUsernameToken),
                'SessionToken'=>$oToken->getSessionToken());

            try
            {
                //Disconnect
                $this->m_clSOAPProxy->disconnect($TabHeader);
                $this->__eventDispatcher->dispatch('session.disconnect', new GenericEvent($oToken->getSessionToken()));
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
        }

	}
}