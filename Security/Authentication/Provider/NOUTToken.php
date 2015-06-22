<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider;


use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class NOUTToken extends UsernamePasswordToken
{

	/**
	 * @var string
	 */
	protected $m_sTimeZone;

	/**
	 * @var string
	 */
	protected $m_sSessionToken;

	/**
	 * @var Langage
	 */
	protected $m_clLangage;


	/**
	 * {@inheritdoc}
	 */
	public function __construct($user, $credentials, $providerKey, array $roles = array())
	{
		parent::__construct($user, $credentials, $providerKey, $roles);
	}

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
	 * @param Langage $clLangage
	 * @return $this
	 */
	public function setLangage(Langage $clLangage)
	{
		$this->m_clLangage = $clLangage;
		return $this;
	}

	/**
	 * @return Langage
	 */
	public function getLangage()
	{
		return $this->m_clLangage;
	}

	/**
	 * @param string $sTimeZone
	 * @return $this
	 */
	public function setTimeZone($sTimeZone)
	{
		$this->m_sTimeZone = $sTimeZone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getTimeZone()
	{
		return $this->m_sTimeZone;
	}


	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array($this->m_sSessionToken, $this->m_sTimeZone, is_null($this->m_clLangage) ? '' : $this->m_clLangage->serialize(), parent::serialize()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list($this->m_sSessionToken, $this->m_sTimeZone, $sLangage, $parentStr) = unserialize($serialized);
		$this->m_clLangage = new Langage('', '');
		$this->m_clLangage->unserialize($sLangage);
		parent::unserialize($parentStr);
	}


	const SESSION_LastTimeZone='LastTimeZone';
} 