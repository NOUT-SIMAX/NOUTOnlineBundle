<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/11/14
 * Time: 11:34
 */

namespace NOUT\Bundle\SessionManagerBundle\Entity;


use Symfony\Component\Security\Core\User\UserInterface;

class User implements UserInterface
{
	/**
	 * @var string
	 */
	protected $m_sUsername;
	/**
	 * @var string
	 */
	protected $m_sPassword;
	/**
	 * @var array
	 */
	protected $m_TabRoles;


	/**
	 * @inheritdoc
	 */
	public function getRoles()
	{
		return $this->m_TabRoles;
	}

	/**
	 * @param $sRole
	 * @return $this
	 */
	public function addRole($sRole)
	{
		$this->m_TabRoles[]=$sRole;
		return $this;
	}


	/**
	 * @inheritdoc
	 */
	public function getPassword()
	{
		return $this->m_sPassword;
	}

	/**
	 * @param $sPass
	 * @return $this
	 */
	public function setPassword($sPass)
	{
		$this->m_sPassword=$sPass; return $this;
	}

	/**
	 * Returns the salt that was originally used to encode the password.
	 *
	 * This can return null if the password was not encoded using a salt.
	 *
	 * @return string|null The salt
	 */
	public function getSalt() { return null; }

	/**
	 * @inheritdoc
	 */
	public function getUsername()
	{
		return $this->m_sUsername;
	}

	/**
	 * @param mixed $username
	 */
	public function setUsername($username)
	{
		$this->m_sUsername = $username;
		return $this;
	}

	/**
	 * Removes sensitive data from the user.
	 *
	 * This is important if, at any given point, sensitive information like
	 * the plain-text password is stored on this object.
	 */
	public function eraseCredentials() {}
}