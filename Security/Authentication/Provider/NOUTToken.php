<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
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
     * le mot de passe en clair de l'utilisateur intranet
     * @var string m_sLoginPasswordSIMAX
     */
    protected $m_sPasswordSIMAX;

    /**
     * @var bool m_bExtranet
     */
    protected $m_bExtranet = false;

    /**
     * le login de l'utilisateur extranet
     * @var string m_sLoginExtranet
     */
    protected $m_sLoginExtranet;

    /**
     * version du noutonline
     * @var NOUTOnlineVersion m_clVersionNO
     */
    protected $m_clVersionNO;

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
    public function getLoginExtranet()
    {
        return $this->m_sLoginExtranet;
    }

    /**
     * @param string $sLoginExtranet
     */
    public function setLoginExtranet($sLoginExtranet)
    {
        $this->m_sLoginExtranet = $sLoginExtranet;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExtranet()
    {
        return $this->m_bExtranet;
    }

    /**
     * @param boolean $bExtranet
     */
    public function setExtranet($bExtranet)
    {
        $this->m_bExtranet = $bExtranet;
    }



    /**
     * @return string
     */
    public function getPasswordSIMAX()
    {
        return $this->m_sPasswordSIMAX;
    }

    /**
     * un alias de getUsername
     * @return string
     */
    public function getLoginSIMAX()
    {
        return $this->getUsername();
    }



    /**
     * @param string $sLoginPassword
     */
    public function setPasswordSIMAX($sLoginPassword)
    {
        $this->m_sPasswordSIMAX = $sLoginPassword;
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
     * @return NOUTOnlineVersion
     */
    public function getVersionNO()
    {
        return $this->m_clVersionNO;
    }

    /**
     * @param string|NOUTOnlineVersion $versionNO
     * @return $this
     */
    public function setVersionNO($versionNO)
    {
        if ($versionNO instanceof NOUTOnlineVersion)
        {
            $this->m_clVersionNO = $versionNO;
        }
        else
        {
            $this->m_clVersionNO = new NOUTOnlineVersion($versionNO);
        }
        return $this;
    }

    /**
     * vrai si la version courante est supérieur (ou égal suivant $bInclu)
     * @param      $sVersionMin
     * @param bool $bInclu
     */
    public function isVersionSup($sVersionMin, $bInclu)
    {
        if (is_null($this->m_clVersionNO)){
            return false;
        }
        return $this->m_clVersionNO->isVersionSup($sVersionMin, $bInclu);
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
                $this->m_sPasswordSIMAX,
                $this->m_bExtranet,
                $this->m_sLoginExtranet,
                $this->m_clVersionNO->get(),
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
        $nbElem = count($aUnserialised);
        if ($nbElem>=9)
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $this->m_sPasswordSIMAX, $this->m_bExtranet, $this->m_sLoginExtranet, $versionNO, $sLangage, $parentStr) = $aUnserialised;
        }
        elseif ($nbElem>=8)
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $this->m_sPasswordSIMAX, $this->m_bExtranet, $this->m_sLoginExtranet, $sLangage, $parentStr) = $aUnserialised;
        }
        elseif ($nbElem>=6)
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $this->m_sPasswordSIMAX, $sLangage, $parentStr) = $aUnserialised;
        }
        else
        {
            list($this->m_sIP, $this->m_sSessionToken, $this->m_sTimeZone, $sLangage, $parentStr) = $aUnserialised;
        }

        $this->m_clVersionNO = new NOUTOnlineVersion($versionNO);
		$this->m_clLangage = new Langage('', '');
		$this->m_clLangage->unserialize($sLangage);
		parent::unserialize($parentStr);
	}


	const SESSION_LastTimeZone='LastTimeZone';
} 