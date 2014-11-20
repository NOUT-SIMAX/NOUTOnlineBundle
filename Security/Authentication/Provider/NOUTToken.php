<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider;


use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

class NOUTToken extends UsernamePasswordToken
{
	/**
	 * @var string
	 */
	protected $m_sSessionToken;

	/**
	 * @param string $sSessionToken
	 */
	public function setSessionToken($sSessionToken)
	{
		$this->m_sSessionToken = $sSessionToken;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSessionToken()
	{
		return $this->m_sSessionToken;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array($this->m_sSessionToken, parent::serialize()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list($this->m_sSessionToken, $parentStr) = unserialize($serialized);
		parent::unserialize($parentStr);
	}
} 