<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 17/11/14
 * Time: 11:51
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Core\User;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;

use NOUT\Bundle\SessionManagerBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;


class NOUTOnlineUserProvider implements UserProviderInterface
{
	/**
	 * @var RESTProxy
	 */
	private $m_clRESTProxy;

	public function __construct(OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue)
	{
		$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);
	}

	/**
	 * @param string $username
	 * @return User|UserInterface
	 * @throws \Symfony\Component\Security\Core\Exception\UsernameNotFoundException
	 */
	public function loadUserByUsername($username)
	{
		// Try service
		$nTypeUtilisateur = $this->m_clRESTProxy->nGetUserExists($username);
		if ($nTypeUtilisateur != RESTProxy::TYPEUTIL_NONE)
		{
			// Set some fields
			$user = new User();
			$user->setUsername($username);
			$user->addRole($nTypeUtilisateur==RESTProxy::TYPEUTIL_UTILISATEUR ? 'ROLE_USER' : 'ROLE_SUPERVISEUR');
			return $user;
		}

		throw new UsernameNotFoundException(sprintf('No record found for user %s', $username));
	}

	/**
	 * @param UserInterface $user
	 * @return User|UserInterface
	 */
	public function refreshUser(UserInterface $user)
	{
		return $this->loadUserByUsername($user->getUsername());
	}

	/**
	 * @param string $class
	 * @return bool
	 */
	public function supportsClass($class)
	{
		return $class === 'NOUT\Bundle\SessionManagerBundle\Entity\User';
	}
}