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
use Symfony\Component\Security\Guard\Token\GuardTokenInterface;

//class NOUTToken extends UsernamePasswordToken implements GuardTokenInterface, TokenWithNOUTOnlineVersionInterface
class NOUTToken extends UsernamePasswordToken implements TokenInterface, TokenWithNOUTOnlineVersionInterface
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

    /** @var bool  */
    protected $m_bSuperviseur=false;

    /** @var string  */
    protected $m_nIDUser='';

    /** @var array  */
    protected $m_aMultiLanguage= [];

    /** @var int  */
    protected $m_nCodeLangue = 0;

    /**
     * {@inheritdoc}
     */
    public function __construct($user/*, $credentials*/,  $providerKey, array $roles = array())
    {
        parent::__construct($user/*, $credentials*/, $providerKey, $roles);
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    public function getUser()
    {
        if (!empty($this->m_sSessionToken)){
            return parent::getUser();
        }
        return null;
    }

    public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier() : string
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
    public function setInfoLangage(Langage $clLangage): NOUTToken
    {
        $this->m_clLangage = $clLangage;
        return $this;
    }

    /**
     * @return Langage
     */
    public function getInfoLangage() : ?Langage
    {
        return $this->m_clLangage;
    }

    /**
     * @param array $aLang
     * @return $this
     */
    public function setMultiLanguage(array $aLang) : NOUTToken
    {
        $this->m_aMultiLanguage = $aLang;
        return $this;
    }

    /**
     * @return array
     */
    public function getMultiLanguage(): array
    {
        return $this->m_aMultiLanguage;
    }

    /**
     * @param int $nCodeLangue
     * @return $this
     */
    public function setSessionCodeLangue(int $nCodeLangue) : NOUTToken
    {
        $this->m_nCodeLangue = $nCodeLangue;
        return $this;
    }

    /**
     * @return int
     */
    public function getSessionCodeLangue() : int
    {
        return $this->m_nCodeLangue;
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
     * @param string $nID
     * @param bool   $bSuperviseur
     * @return $this
     */
    public function setInfoUserConnected(string $nID, bool $bSuperviseur): NOUTToken
    {
        $this->m_nIDUser = $nID;
        $this->m_bSuperviseur = $bSuperviseur;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuperviseur() : bool
    {
        return $this->m_bSuperviseur;
    }

    /**
     * @return string
     */
    public function nGetIDUser(): string
    {
        return $this->m_nIDUser;
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
            'superviseur' => $this->m_bSuperviseur,
            'userID' => $this->m_nIDUser,
            'version' => $this->getVersionNO(),
            'language' => is_null($this->m_clLangage) ? null : $this->m_clLangage->forSerialization(),
            'multilangue' => $this->m_aMultiLanguage,
            'sessioncodelangue' => $this->m_nCodeLangue,
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
        if (isset($aUnserialised['superviseur'])){
            $this->m_bSuperviseur = boolval($aUnserialised['superviseur']);
        }
        if (isset($aUnserialised['userID'])){
            $this->m_nIDUser = $aUnserialised['userID'];
        }
        if (isset($aUnserialised['multilangue'])){
            $this->m_aMultiLanguage = $aUnserialised['multilangue'];
        }
        if (isset($aUnserialised['sessioncodelangue'])){
            $this->m_nCodeLangue = $aUnserialised['sessioncodelangue'];
        }

        $this->m_sNameToDisplay=$aUnserialised['name'];
        parent::__unserialize($aUnserialised['parent_data']);
    }

    const SESSION_LastTimeZone='LastTimeZone';
} 