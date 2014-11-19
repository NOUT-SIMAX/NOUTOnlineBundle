<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 11:46
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider;

use Symfony\Component\Security\Core\Encoder\EncoderFactoryInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\AuthenticationServiceException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\Security\Core\Authentication\Provider\UserAuthenticationProvider;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;

class NOUTOnlineAuthenticationProvider extends UserAuthenticationProvider
{
	/**
	 * @var \Symfony\Component\Security\Core\User\UserProviderInterface
	 */
	private $userProvider;
	/**
	 * @var \Symfony\Component\Security\Core\Encoder\EncoderFactory
	 */
	private $encoderFactory;

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
		parent::__construct($userChecker, $providerKey, $hideUserNotFoundExceptions);
		$this->userProvider   = $userProvider;
		$this->encoderFactory = $encoderFactory; // usually this is responsible for validating passwords

		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function checkAuthentication(UserInterface $user, UsernamePasswordToken $token)
	{
		$currentUser = $token->getUser();

		if ($currentUser instanceof UserInterface)
		{
			if ($currentUser->getPassword() !== $user->getPassword())
			{
				throw new BadCredentialsException('The credentials were changed from another session.');
			}
		}
		else
		{
			if (!$presentedPassword = $token->getCredentials())
			{
				throw new BadCredentialsException('The presented password cannot be empty.');
			}

			if (! $this->service->authenticate($token->getUser(), $presentedPassword))
			{
				throw new BadCredentialsException('The presented password is invalid.');
			}
		}
	}

	/**
	 * {@inheritdoc}
	 */
	protected function retrieveUser($username, UsernamePasswordToken $token)
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
}




