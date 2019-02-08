<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 11:46
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\ClientInformation;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExtranetUserType;
use NOUT\Bundle\SessionManagerBundle\Entity\ConfigExtranet;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\Role\SwitchUserRole;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;


/**
 * classe basée sur Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider
 *
 *
 * Class NOUTOnlineAuthenticationProvider
 * @package NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider
 */
class NOUTOnlineAuthenticationProvider implements AuthenticationProviderInterface
{
	/**
	 * @var \Symfony\Component\Security\Core\User\UserProviderInterface
	 */
	private $userProvider;
	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

	private $hideUserNotFoundExceptions;
	private $userChecker;
	private $providerKey;

	/**
	 * @var SOAPProxy
	 */
	private $m_clSOAPProxy;

	/**
	 * @var RESTProxy
	 */
	private $m_clRESTProxy;

	/**
	 * @var ClientInformation
	 */
	private $m_clClientInformation;

    /**
     * @var ConfigurationDialogue
     */
    private $m_clConfigDialogue;

	/**
     * @var ConfigExtranet
     */
    private $m_clConfigExtranet;



	/**
	 * @param ClientInformation $clClientInfo
	 * @param ConfigExtranet $clConfigExtranet
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 * @param UserProviderInterface $userProvider
	 * @param UserCheckerInterface $userChecker
	 * @param $providerKey
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param bool $hideUserNotFoundExceptions
	 */
	public function __construct(ClientInformation $clClientInfo,
								ConfigExtranet $clConfigExtranet,
                                OnlineServiceFactory $serviceFactory,
                                ConfigurationDialogue $configurationDialogue,
                                UserProviderInterface $userProvider,
                                UserCheckerInterface $userChecker,
                                $providerKey,
                                EncoderFactoryInterface $encoderFactory,
                                $hideUserNotFoundExceptions = true )
	{

		if (empty($providerKey))
		{
			throw new \InvalidArgumentException('$providerKey must not be empty.');
		}

		$this->userChecker = $userChecker;
		$this->providerKey = $providerKey;
		$this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;

		$this->m_clConfigExtranet = $clConfigExtranet;


		$this->userProvider   = $userProvider;
		$this->encoderFactory = $encoderFactory; // usually this is responsible for validating passwords

		$this->m_clClientInformation = $clClientInfo;
        $this->m_clConfigDialogue = $configurationDialogue;

		try{
			$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
			$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);
		}
		catch(\Exception $e){
			$this->m_clSOAPProxy = null;
			$this->m_clRESTProxy = null;
			$this->last_exception = $e;
		}

	}

	/**
	 * @param TokenInterface $token
	 * @return NOUTToken
	 * @throws \Exception
	 */
    public function authenticate(TokenInterface $token)
	{
		if (!$this->supports($token))
		{
			throw new AuthenticationServiceException('The token is not supported.');
		}

		$username = $token->getUsername();
		if (empty($username))
        {
            //$username = 'NONE_PROVIDED';
            throw new UsernameNotFoundException('You must enter a username.');
        }

		try
		{
			$user = $this->_RetrieveUser($username, $token);
		}
		catch (UsernameNotFoundException $notFound)
		{
			if ($this->hideUserNotFoundExceptions)
			{
				throw new BadCredentialsException('Bad credentials', 0, $notFound);
			}
			$notFound->setUsername($username);
			throw $notFound;
		}

		if (!$user instanceof UserInterface)
		{
			throw new AuthenticationServiceException('retrieveUser() must return a UserInterface.');
		}

		try
		{
			$this->userChecker->checkPreAuth($user);
			$sTokenSession = $this->_CheckAuthentication($user, $token);
			$this->userChecker->checkPostAuth($user);
		}
		catch (BadCredentialsException $e)
		{
			if ($this->hideUserNotFoundExceptions)
			{
				throw new BadCredentialsException('Bad credentials', 0, $e);
			}

			throw $e;
		}

		if ($token instanceof NOUTToken)
		{
            // Si on est en identification extranet
            if($this->m_clConfigExtranet->isExtranet())
            {
                $sUser      = $this->m_clConfigExtranet->getUser();
                $sPassword  = $this->m_clConfigExtranet->getPassword();
                // $sFormID    = $this->m_clConfigExtranet->getForm();  // Plus loin
            }
            else
            {
                $sUser      = $token->getLoginSIMAX();
                $sPassword  = $token->getPasswordSIMAX(); //le membre login password contient le mdp mis par l'utilisateur, il est là pour ne pas être vider
            }


			$authenticatedToken = new NOUTToken($sUser, '', $this->providerKey, $this->_aGetRoles($user, $token));
			$authenticatedToken->setTimeZone($token->getTimeZone());
			$authenticatedToken->setAttributes($token->getAttributes());
			$authenticatedToken->setSessionToken($sTokenSession);
			$authenticatedToken->setIP($this->m_clClientInformation->getIP());
            $authenticatedToken->setPasswordSIMAX($sPassword);
            if ($this->m_clConfigExtranet->isExtranet())
            {
                $authenticatedToken->setLoginExtranet($username);
                $authenticatedToken->setExtranet(true);
            }

			$clIdentification = new Identification();

			$clIdentification->m_clUsernameToken = new UsernameToken(
                $sUser,
                $sPassword,
                $this->m_clConfigDialogue->getModeAuth(),
                $this->m_clConfigDialogue->getSecret()
            );

			$clIdentification->m_sTokenSession = $sTokenSession;
			$clIdentification->m_sIDContexteAction = '';
			$clIdentification->m_bAPIUser = true;


			$sVersionLangage = $this->m_clRESTProxy->sGetChecksumLangage($clIdentification);
			$clIdentification->m_clUsernameToken->ComputeCryptedPassword(); //recalcule le mot de passe crypté
			$sVersionIcone = $this->m_clRESTProxy->sGetChecksum(Langage::TABL_ImageCatalogue, $clIdentification);

			$authenticatedToken->setLangage(new Langage($sVersionLangage, $sVersionIcone));

            $clVersionNO = $this->m_clRESTProxy->clGetVersion();
            $authenticatedToken->setVersionNO($clVersionNO);

			return $authenticatedToken;
		}



		throw new AuthenticationServiceException('The token is not a NOUTToken.');

	}

	/**
	 * {@inheritdoc}
	 */
	public function supports(TokenInterface $token)
	{
		return ($token instanceof UsernamePasswordToken) && ($this->providerKey === $token->getProviderKey());
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _CheckAuthentication(UserInterface $user, UsernamePasswordToken $token)
	{
		$currentUser = $token->getUser();

		if ($currentUser instanceof UserInterface)
		{
			if ($currentUser->getPassword() !== $user->getPassword())
			{
				throw new BadCredentialsException('The credentials were changed from another session.');
			}

			if ($token instanceof NOUTToken)
			{
				if (empty($token->getSessionToken()))
				{
					throw new BadCredentialsException('The session token is empty.');
				}
				return $token->getSessionToken();
			}

			throw new AuthenticationServiceException('The token is not a NOUTToken.');
		}
		else
		{
            // Si on est en identification extranet
            if($this->m_clConfigExtranet->isExtranet())
            {
                $sUser      = $this->m_clConfigExtranet->getUser();
                $sPassword  = $this->m_clConfigExtranet->getPassword();
                // $sFormID    = $this->m_clConfigExtranet->getForm();  // Plus loin
            }

            else
            {
                $sUser      = $token->getLoginSIMAX();
                $sPassword  = $token->getPasswordSIMAX();
            }

            $oUsernameToken = new UsernameToken(
                $sUser,
                $sPassword,
                $this->m_clConfigDialogue->getModeAuth(),
                $this->m_clConfigDialogue->getSecret());

            $oGetTokenSessionParam = new GetTokenSession();

			if(is_null($this->m_clSOAPProxy))
			{
				throw $this->last_exception;
			}

            $oGetTokenSessionParam->UsernameToken = $this->m_clSOAPProxy->getUsernameTokenForWdsl($oUsernameToken);
            $oGetTokenSessionParam->DefaultClientLanguageCode = $user->getLocale();

            if($this->m_clConfigExtranet->isExtranet())
            {
                $oExtranetUsernameToken = new UsernameToken(
                    $token->getLoginSIMAX(),
                    $token->getPasswordSIMAX(),
                    $this->m_clConfigDialogue->getModeAuth(),
                    $this->m_clConfigDialogue->getSecret());

                // $sFormID    = $this->m_clConfigExtranet->getForm();  // Plus loin
                $oGetTokenSessionParam->ExtranetUser = new ExtranetUserType();
                $oGetTokenSessionParam->ExtranetUser->UsernameToken = $this->m_clSOAPProxy->getUsernameTokenForWdsl($oExtranetUsernameToken);
                $oGetTokenSessionParam->ExtranetUser->Form = $this->m_clConfigExtranet->getForm();
            }

			try
			{
				$clReponseXML = $this->m_clSOAPProxy->getTokenSession($oGetTokenSessionParam);
			}
			catch(\Exception $e)
			{
				if ($e instanceof SOAPException)
				{
					//erreur à la connexion
					$clReponseXML = $this->m_clSOAPProxy->getXMLResponseWS();
					if ($clReponseXML instanceof XMLResponseWS)
					{
						throw new BadCredentialsException($clReponseXML->getMessError());
					}
					else
					{
						throw new BadCredentialsException('The presented password is invalid.');
					}
				}

				throw $e;
			}

			return $clReponseXML->sGetTokenSession();
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function _RetrieveUser($username, UsernamePasswordToken $token)
	{
		$user = $token->getUser();
		if ($user instanceof UserInterface)
		{
			return $user;
		}

		try
		{
			$user = $this->userProvider->loadUserByUsername($username);
            $user->setLocale($token->getLocale());
			if (!$user instanceof UserInterface)
			{
				throw new AuthenticationServiceException('The user provider must return a UserInterface object.');
			}

			return $user;
		}
		catch (UsernameNotFoundException $notFound)
		{
			throw $notFound;
		}
		catch (\Exception $repositoryProblem)
		{
			throw new AuthenticationServiceException($repositoryProblem->getMessage(), 0, $repositoryProblem);
		}
	}



	/**
	 * Retrieves roles from user and appends SwitchUserRole if original token contained one.
	 *
	 * @param UserInterface  $user  The user
	 * @param TokenInterface $token The token
	 *
	 * @return Role[] The user roles
	 */
	private function _aGetRoles(UserInterface $user, TokenInterface $token)
	{
		$roles = $user->getRoles();

		foreach ($token->getRoles() as $role) {
			if ($role instanceof SwitchUserRole) {
				$roles[] = $role;

				break;
			}
		}

		return $roles;
	}
}




