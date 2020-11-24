<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

class NOUTToken extends UsernamePasswordToken implements GuardTokenInterface
{
    /**
     * @var Langage
     */
    protected $m_clLangage;

    /**
     * version du noutonline
     * @var NOUTOnlineVersion m_clVersionNO
     */
    protected $m_clVersionNO;

	/**
	 * @var string
	 */
	protected $m_sTimeZone;

	/**
	 * @var string
	 */
	protected $m_sLocale;

	/**
	 * @var string
	 */
	protected $m_sSessionToken;

	/**
	 * @var string m_sIP
	 */
	protected $m_sIP;

	/** @var string */
	protected $m_sNameToDisplay='';

	/** @var UsernameToken|null */
	protected $m_oUsernameToken;

	/** @var UsernameToken|null */
	protected $m_oExtranetUsernameToken=null;

	/**
	 * {@inheritdoc}
	 */
	public function __construct($user, $credentials, $providerKey, array $roles = array())
	{
		parent::__construct($user, $credentials, $providerKey, $roles);
	}


    /**
     * @return UsernameToken|null
     */
    public function getExtranetUsernameToken() : ?UsernameToken
    {
        return $this->m_oExtranetUsernameToken;
    }

    /**
     * @param UsernameToken $oUsernameToken
     * @return $this
     */
    public function setExtranetUsernameToken(UsernameToken $oUsernameToken)
    {
        $this->m_oExtranetUsernameToken = $oUsernameToken;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExtranet()
    {
        return !is_null($this->m_oExtranetUsernameToken);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setNameToDisplay(string $name)
    {
        $this->m_sNameToDisplay = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameToDisplay() : string
    {
        return $this->m_sNameToDisplay;
    }


    /**
     * @param bool $bCompute recalcule le created, nonce, password
     * @return UsernameToken
     */
    public function getUsernameToken() : ?UsernameToken
    {
        $this->m_oUsernameToken->Compute();
        return $this->m_oUsernameToken;
    }

    /**
     * un alias de getUsername
     * @param UsernameToken $oUsernameToken
     * @return $this
     */
    public function setUsernameToken(UsernameToken $oUsernameToken)
    {
        $this->m_oUsernameToken = $oUsernameToken;
        return $this;
    }

	/**
	 * @param string $sSessionToken
     * @return $this
	 */
	public function setSessionToken(string $sSessionToken)
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
	public function setTimeZone(string $sTimeZone)
	{
		$this->m_sTimeZone = $sTimeZone;
		return $this;
	}

    /**
     * @param string $sLocale
     * @return $this$locale
     */
    public function setLocale(string $sLocale) {
        $this->m_sLocale = $sLocale;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone() {
        return $this->m_sTimeZone;
    }

	/**
	 * @return string
	 */
	public function getLocale()
	{
		return $this->m_sLocale;
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
	public function setIP(string $sIP)
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
     * @param string $sVersionMin
     * @param bool $bInclu
     * @return bool
     */
    public function isVersionSup(string $sVersionMin, bool $bInclu) : bool
    {
        if (is_null($this->m_clVersionNO)){
            return false;
        }

        if ($this->m_clVersionNO instanceof  NOUTOnlineVersion)
        {
            return $this->m_clVersionNO->isVersionSup($sVersionMin, $bInclu);
        }

        $clVersion = new NOUTOnlineVersion($this->m_clVersionNO);
        return $clVersion->isVersionSup($sVersionMin, $bInclu);
    }



	/**
	 * {@inheritdoc}
	 */
    public function __serialize(): array
    {
        if (is_null($this->m_clVersionNO)){
            $sVersion='';
        }
        elseif ($this->m_clVersionNO instanceof NOUTOnlineVersion) {
            $sVersion = $this->m_clVersionNO->get();
        }
        else {
            $sVersion = $this->m_clVersionNO;
        }
        return [
            'ip' => $this->m_sIP,
            'token' => $this->m_sSessionToken,
            'timezone' => $this->m_sTimeZone,
            'locale' => $this->m_sLocale,
            'name' => $this->m_sNameToDisplay,
            'user' => [
                'class' => get_class($this->m_oUsernameToken),
                'data' => $this->m_oUsernameToken->forSerialization()
            ],
            'extranet' => !$this->m_oExtranetUsernameToken ? null : [
                'class' => get_class($this->m_oExtranetUsernameToken),
                'data' => $this->m_oExtranetUsernameToken->forSerialization()
            ],
            'version' => $sVersion,
            'language' => is_null($this->m_clLangage) ? null : $this->m_clLangage->forSerialization(),
            'parent_data' => parent::__serialize()
        ];
    }

	/**
	 * {@inheritdoc}
     * @throw \Exception
	 */
	public function __unserialize(array $aUnserialised): void
	{
        if (!array_key_exists('ip', $aUnserialised)){
            throw new UnserializeTokenException('Invalid Token');
        }


        $this->m_sIP = $aUnserialised['ip'];
        $this->m_sSessionToken = $aUnserialised['token'];
        $this->m_sTimeZone = $aUnserialised['timezone'];
        $this->m_sLocale = $aUnserialised['locale'];

        $this->m_clLangage = new Langage();
        $this->m_clLangage->fromSerialization($aUnserialised['language']);
        $this->m_clVersionNO = new NOUTOnlineVersion($aUnserialised['version']);

        $this->m_oUsernameToken = new $aUnserialised['user']['class']();
        $this->m_oUsernameToken->fromSerialization($aUnserialised['user']['data']);

        if (!is_null($aUnserialised['extranet'])){
            $this->m_oExtranetUsernameToken = new $aUnserialised['extranet']['class']();
            $this->m_oExtranetUsernameToken->fromSerialization($aUnserialised['extranet']['data']);
        }

        $this->m_sNameToDisplay=$aUnserialised['name'];
        parent::__unserialize($aUnserialised['parent_data']);
	}

	const SESSION_LastTimeZone='LastTimeZone';
} 