<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 15/12/14
 * Time: 17:10
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Firewall;

use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Http\Firewall\UsernamePasswordFormAuthenticationListener;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategyInterface;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\HttpUtils;

class NOUTOnlineListener extends UsernamePasswordFormAuthenticationListener
{

	protected $csrfTokenManager;
	/**
	 * {@inheritdoc}
	 */
	public function __construct(SecurityContextInterface $securityContext, AuthenticationManagerInterface $authenticationManager, SessionAuthenticationStrategyInterface $sessionStrategy, HttpUtils $httpUtils, $providerKey, AuthenticationSuccessHandlerInterface $successHandler, AuthenticationFailureHandlerInterface $failureHandler, array $options = array(), LoggerInterface $logger = null, EventDispatcherInterface $dispatcher = null, $csrfTokenManager = null)
	{
		parent::__construct($securityContext, $authenticationManager, $sessionStrategy, $httpUtils, $providerKey, $successHandler, $failureHandler, array_merge(array(
			'timezone_parameter' => 'm_sTimeZone',
		), $options), $logger, $dispatcher);

		$this->csrfTokenManager = $csrfTokenManager;
	}

	/**
	 * Performs authentication.
	 *
	 * @param Request $request A Request instance
	 *
	 * @return TokenInterface|Response|null The authenticated token, null if full authentication is not possible, or a Response
	 *
	 * @throws AuthenticationException if the authentication fails
	 */
	protected function attemptAuthentication(Request $request)
	{
		if (null !== $this->csrfTokenManager) {
			$csrfToken = $request->get($this->options['csrf_parameter'], null, true);

			if (false === $this->csrfTokenManager->isTokenValid(new CsrfToken($this->options['intention'], $csrfToken))) {
				throw new InvalidCsrfTokenException('Invalid CSRF token.');
			}
		}

		if ($this->options['post_only'])
		{
			$username = trim($request->request->get($this->options['username_parameter'], null, true));
			$password = $request->request->get($this->options['password_parameter'], null, true);
			$timezone = $request->request->get('m_sTimeZone', null, true);
		}
		else
		{
			$username = trim($request->get($this->options['username_parameter'], null, true));
			$password = $request->get($this->options['password_parameter'], null, true);
			$timezone = $request->get('m_sTimeZone', null, true);
		}

		$request->getSession()->set(Security::LAST_USERNAME, $username);
		$request->getSession()->set(NOUTToken::SESSION_LastTimeZone, $timezone);

		$clToken = new NOUTToken($username, $password, $this->providerKey);
		$clToken->setTimeZone($timezone);

		try
		{
			$clAuth =  $this->authenticationManager->authenticate($clToken);
		}
		catch (\Exception $e)
		{
			throw $e;
		}

		$request->getSession()->remove(NOUTToken::SESSION_LastTimeZone);
		return $clAuth;
	}

} 