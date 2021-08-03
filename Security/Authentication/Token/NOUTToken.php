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
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Authenticator\Token\PostAuthenticationToken;

class NOUTToken extends PostAuthenticationToken implements TokenInterface, TokenWithNOUTOnlineVersionInterface
{
    use TraitTokenWithNOUTOnlineVersion;

    /**
     * @var Langage|null
     */
    protected $m_clLangage=null;

	/**
	 * @var string
	 */
	protected $m_sTimeZone='';

	/**
	 * @var string
	 */
	protected $m_sLocale='';

	/**
	 * @var string
	 */
	protected $m_sSessionToken='';

	/**
	 * @var string m_sIP
	 */
	protected $m_sIP='';

	/** @var string */
	protected $m_sNameToDisplay='';

	/** @var UsernameToken|null */
	protected $m_oUsernameToken=null;

	/** @var UsernameToken|null */
	protected $m_oExtranetUsernameToken=null;

	/** @var bool  */
	protected $m_bAnonyme=false;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(UserInterface $user, $providerKey, array $roles = array())
	{
		parent::__construct($user, $providerKey, $roles);
	}

    /**
     * {@inheritdoc}
     */
	public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier(): string
    {
        if ($this->m_oUsernameToken instanceof UsernameToken){
            return $this->m_oUsernameToken->Username;
        }

        return parent::getUserIdentifier();
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
    public function setExtranetUsernameToken(UsernameToken $oUsernameToken): NOUTToken
    {
        $this->m_oExtranetUsernameToken = $oUsernameToken;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExtranet() : bool
    {
        return !is_null($this->m_oExtranetUsernameToken);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setNameToDisplay(string $name): NOUTToken
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
     * @return UsernameToken|null
     * @throws \Exception
     */
    public function getUsernameToken() : ?UsernameToken
    {
        return $this->m_oUsernameToken;
    }

    /**
     * un alias de getUsername
     * @param UsernameToken $oUsernameToken
     * @return $this
     */
    public function setUsernameToken(UsernameToken $oUsernameToken): NOUTToken
    {
        $this->m_oUsernameToken = $oUsernameToken;
        return $this;
    }

	/**
	 * @param string $sSessionToken
     * @return $this
	 */
	public function setSessionToken(string $sSessionToken): NOUTToken
	{
		$this->m_sSessionToken = $sSessionToken;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getSessionToken() : string
	{
		return $this->m_sSessionToken;
	}

	/**
	 * @param Langage $clLangage
	 * @return $this
	 */
	public function setLangage(Langage $clLangage): NOUTToken
	{
		$this->m_clLangage = $clLangage;
		return $this;
	}

	/**
	 * @return Langage
	 */
	public function getLangage() : ?Langage
	{
		return $this->m_clLangage;
	}

	/**
	 * @param string $sTimeZone
	 * @return $this
	 */
	public function setTimeZone(string $sTimeZone) : NOUTToken
	{
		$this->m_sTimeZone = $sTimeZone;
		return $this;
	}

    /**
     * @param string $sLocale
     * @return $this
     */
    public function setLocale(string $sLocale) : NOUTToken {
        $this->m_sLocale = $sLocale;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone() : string {
        return $this->m_sTimeZone;
    }

	/**
	 * @return string
	 */
	public function getLocale() : string
	{
		return $this->m_sLocale;
	}

	/**
	 * @return string
	 */
	public function getIP() : string
	{
		return $this->m_sIP;
	}

	/**
	 * @param string $sIP
	 * @return $this
	 */
	public function setIP(string $sIP) : NOUTToken
	{
		$this->m_sIP = $sIP;
		return $this;
	}

    /**
     * @return bool
     */
    public function isAnonyme(): bool
    {
        return $this->m_bAnonyme;
    }

    /**
     * @param bool $bAnonyme
     * @return NOUTToken
     */
    public function setAnonyme(bool $bAnonyme): NOUTToken
    {
        $this->m_bAnonyme = $bAnonyme;
        return $this;
    }



	/**
	 * {@inheritdoc}
	 */
    public function __serialize(): array
    {
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
            'anonyme' => $this->m_bAnonyme,
            'version' => $this->getVersionNO(),
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
        if (isset($aUnserialised['anonyme'])){
            $this->m_bAnonyme = boolval($aUnserialised['anonyme']);
        }

        $this->m_sNameToDisplay=$aUnserialised['name'];
        parent::__unserialize($aUnserialised['parent_data']);
	}

	const SESSION_LastTimeZone='LastTimeZone';
} 