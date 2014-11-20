<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 11:46
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider;

use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;

use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
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
 * @package NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider
 */
class NOUTOnlineAuthenticationProvider extends AuthenticationProviderManager
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
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 * @param UserProviderInterface $userProvider
	 * @param UserCheckerInterface $userChecker
	 * @param $providerKey
	 * @param EncoderFactoryInterface $encoderFactory
	 * @param bool $hideUserNotFoundExceptions
	 */
	public function __construct(OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue, UserProviderInterface $userProvider, UserCheckerInterface $userChecker, $providerKey, EncoderFactoryInterface $encoderFactory, $hideUserNotFoundExceptions = true )
	{
		if (empty($providerKey))
		{
			throw new \InvalidArgumentException('$providerKey must not be empty.');
		}

		$this->userChecker = $userChecker;
		$this->providerKey = $providerKey;
		$this->hideUserNotFoundExceptions = $hideUserNotFoundExceptions;



		$this->userProvider   = $userProvider;
		$this->encoderFactory = $encoderFactory; // usually this is responsible for validating passwords

		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
	}

	/**
	 * {@inheritdoc}
	 */
	public function authenticate(TokenInterface $token)
	{
		if (!$this->supports($token))
			return;

		$username = $token->getUsername();
		if (empty($username))
			$username = 'NONE_PROVIDED';

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

		$authenticatedToken = new NOUTToken($user, $token->getCredentials(), $this->providerKey, $this->_aGetRoles($user, $token));
		$authenticatedToken->setAttributes($token->getAttributes());
		$authenticatedToken->setSessionToken($sTokenSession);

		return $authenticatedToken;
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

			if (strlen($token->getSessionToken())==0)
			{
				throw new BadCredentialsException('The session token is empty.');
			}
			return $token->getSessionToken();
		}
		else
		{
			$presentedPassword = $token->getCredentials();

			$oTokenSession = new GetTokenSession();
			$oTokenSession->UsernameToken = new UsernameToken($user->getUsername(), $presentedPassword);

			try
			{
				$clReponseXML = $this->m_clSOAPProxy->getTokenSession($oTokenSession);
			}
			catch(\Exception $e)
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

			$user->setPassword($presentedPassword);
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
			throw new AuthenticationServiceException($repositoryProblem->getMessage(), $token, 0, $repositoryProblem);
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




