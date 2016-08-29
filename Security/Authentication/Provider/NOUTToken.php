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
	 * @var string m_sIP
	 */
	protected $m_sIP;

    /**
     * @var string m_sLoginPassword
     */
    protected $m_sLoginPassword;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($user, $credentials, $providerKey, array $roles = array())
	{
		parent::__construct($user, $credentials, $providerKey, $roles);
	}

    /**
     * @return string
     */
    public function getLoginPassword()
    {
        return $this->m_sLoginPassword;
    }

    /**
     * @param string $sLoginPassword
     */
    public function setLoginPassword($sLoginPassword)
    {
        $this->m_sLoginPassword = $sLoginPassword;
        return $this;
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
	 * @return string
	 */
	public function getIP()
	{
		return $this->m_sIP;
	}

	/**
	 * @param string $sIP
	 * @return $this
	 */
	public function setIP($sIP)
	{
		$this->m_sIP = $sIP;
		return $this;
	}

	/**
	 * {@inheritdoc}
	 */
	public function serialize()
	{
		return serialize(
            array(
                $this->m_sIP,
                $this->m_sSessionToken,
                $this->m_sTimeZone,
                $this->m_sLoginPassword,
                is_null($this->m_clLangage) ? '' : $this->m_clLangage->serialize()
                , parent::serialize()
            )
        );
	}

	/**
	 * {@inheritdoc}
	 */
	public function unserialize($serialized)
	{
        $aUnserialised = unserialize($serialized);
        if (count($aUnserialised)>=6)
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $this->m_sLoginPassword, $sLangage, $parentStr) = $aUnserialised;
        }
        else
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $sLangage, $parentStr) = $aUnserialised;
        }
		$this->m_clLangage = new Langage('', '');
		$this->m_clLangage->unserialize($sLangage);
		parent::unserialize($parentStr);
	}


	const SESSION_LastTimeZone='LastTimeZone';
} 