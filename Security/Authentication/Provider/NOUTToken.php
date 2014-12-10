<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\NOUTSessionManagerBundle\Security\Authentication\Provider;


use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class NOUTToken extends UsernamePasswordToken
{
	/**
	 * @var string
	 */
	protected $m_sSessionToken;

	/**
	 * @var Langage
	 */
	protected $m_clLangage;

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
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(array($this->m_sSessionToken, $this->m_clLangage->serialize(), parent::serialize()));
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
		list($this->m_sSessionToken, $sLangage, $parentStr) = unserialize($serialized);
		$this->m_clLangage = new Langage('', '');
		$this->m_clLangage->unserialize($sLangage);
		parent::unserialize($parentStr);
	}
} 