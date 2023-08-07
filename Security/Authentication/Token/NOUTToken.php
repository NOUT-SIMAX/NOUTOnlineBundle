<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 20/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\FormElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;

//class NOUTToken extends UsernamePasswordToken implements GuardTokenInterface, TokenWithNOUTOnlineVersionInterface
class NOUTToken extends UsernamePasswordToken implements TokenInterface, TokenWithNOUTOnlineVersionInterface
{
    use TraitTokenWithNOUTOnlineVersion;

    /**
     * @var Langage|null
     */
    protected ?Langage $clLangage =null;

    /**
     * @var string
     */
    protected string $sTimeZone ='';

    /**
     * @var string
     */
    protected string $sLocale ='';

    /**
     * @var string
     */
    protected string $sSessionToken ='';

    /**
     * @var string m_sIP
     */
    protected string $sIP ='';

    /** @var string */
    protected string $sNameToDisplay ='';

    /** @var UsernameToken|null */
    protected ?UsernameToken $oUsernameToken =null;

    /** @var UsernameToken|null */
    protected ?UsernameToken $oExtranetUsernameToken =null;

    /** @var bool  */
    protected bool $bAnonyme =false;

    /** @var bool  */
    protected bool $bSuperviseur =false;

    /** @var string  */
    protected string $nIDUser ='';

    /** @var array  */
    protected array $aMultiLanguage = [];

    /** @var int  */
    protected int $nCodeLangue = 0;

    /** @var bool  */
    protected bool $bWithConfiguration = false;

    /** @var string  */
    protected string $googleApiKey = '';

    /** @var FormElement|null  */
    protected ?FormElement $oResource = null;

    /**
     * {@inheritdoc}
     */
    public function __construct($user,  $providerKey, array $roles = array())
    {
        parent::__construct($user, $providerKey, $roles);
    }

    /**
     * @return \Symfony\Component\Security\Core\User\UserInterface|null
     */
    public function getUser()
    {
        if (!empty($this->sSessionToken)){
            return parent::getUser();
        }
        return null;
    }

    public function getUsername()
    {
        return $this->getUserIdentifier();
    }

    /**
     * @param  string $googleApiKey
     * @return $this
     */
    public function setGoogleApiKey(string $googleApiKey) : NOUTToken
    {
        $this->googleApiKey = $googleApiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getGoogleApiKey() : string
    {
        return $this->googleApiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getUserIdentifier() : string
    {
        if ($this->oUsernameToken instanceof UsernameToken){
            return $this->oUsernameToken->Username;
        }

        return parent::getUserIdentifier();
    }

    /**
     * @return UsernameToken|null
     */
    public function getExtranetUsernameToken() : ?UsernameToken
    {
        return $this->oExtranetUsernameToken;
    }

    /**
     * @param UsernameToken $oUsernameToken
     * @return $this
     */
    public function setExtranetUsernameToken(UsernameToken $oUsernameToken): NOUTToken
    {
        $this->oExtranetUsernameToken = $oUsernameToken;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isExtranet() : bool
    {
        return !is_null($this->oExtranetUsernameToken);
    }

    /**
     * @param string $name
     * @return $this
     */
    public function setNameToDisplay(string $name): NOUTToken
    {
        $this->sNameToDisplay = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getNameToDisplay() : string
    {
        return $this->sNameToDisplay;
    }

    /**
     * @return UsernameToken|null
     * @throws \Exception
     */
    public function getUsernameToken() : ?UsernameToken
    {
        return $this->oUsernameToken;
    }

    /**
     * un alias de getUsername
     * @param UsernameToken $oUsernameToken
     * @return $this
     */
    public function setUsernameToken(UsernameToken $oUsernameToken): NOUTToken
    {
        $this->oUsernameToken = $oUsernameToken;
        return $this;
    }

    /**
     * @param string $sSessionToken
     * @return $this
     */
    public function setSessionToken(string $sSessionToken): NOUTToken
    {
        $this->sSessionToken = $sSessionToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getSessionToken() : string
    {
        return $this->sSessionToken;
    }

    /**
     * @param Langage $clLangage
     * @return $this
     */
    public function setInfoLangage(Langage $clLangage): NOUTToken
    {
        $this->clLangage = $clLangage;
        return $this;
    }

    /**
     * @return Langage
     */
    public function getInfoLangage() : ?Langage
    {
        return $this->clLangage;
    }

    /**
     * @param array $aLang
     * @return $this
     */
    public function setMultiLanguage(array $aLang) : NOUTToken
    {
        $this->aMultiLanguage = $aLang;
        return $this;
    }

    /**
     * @return array
     */
    public function getMultiLanguage(): array
    {
        return $this->aMultiLanguage;
    }

    /**
     * @param int $nCodeLangue
     * @return $this
     */
    public function setSessionCodeLangue(int $nCodeLangue) : NOUTToken
    {
        $this->nCodeLangue = $nCodeLangue;
        return $this;
    }

    /**
     * @return int
     */
    public function getSessionCodeLangue() : int
    {
        return $this->nCodeLangue;
    }

    /**
     * @param string $sTimeZone
     * @return $this
     */
    public function setTimeZone(string $sTimeZone) : NOUTToken
    {
        $this->sTimeZone = $sTimeZone;
        return $this;
    }

    /**
     * @param string $sLocale
     * @return $this
     */
    public function setLocale(string $sLocale) : NOUTToken {
        $this->sLocale = $sLocale;
        return $this;
    }

    /**
     * @return string
     */
    public function getTimeZone() : string {
        return $this->sTimeZone;
    }

    /**
     * @return string
     */
    public function getLocale() : string
    {
        return $this->sLocale;
    }

    /**
     * @return string
     */
    public function getIP() : string
    {
        return $this->sIP;
    }

    /**
     * @param string $sIP
     * @return $this
     */
    public function setIP(string $sIP) : NOUTToken
    {
        $this->sIP = $sIP;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAnonyme(): bool
    {
        return $this->bAnonyme;
    }

    /**
     * @param bool $bAnonyme
     * @return NOUTToken
     */
    public function setAnonyme(bool $bAnonyme): NOUTToken
    {
        $this->bAnonyme = $bAnonyme;
        return $this;
    }

    /**
     * @param string $nID
     * @param bool   $bSuperviseur
     * @return $this
     */
    public function setInfoUserConnected(string $nID, bool $bSuperviseur): NOUTToken
    {
        $this->nIDUser      = $nID;
        $this->bSuperviseur = $bSuperviseur;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSuperviseur() : bool
    {
        return $this->bSuperviseur;
    }

    /**
     * @return string
     */
    public function nGetIDUser(): string
    {
        return $this->nIDUser;
    }

    /**
     * @return bool
     */
    public function isWithConfiguration(): bool
    {
        return $this->bWithConfiguration;
    }

    /**
     * @param bool $bWithConfiguration
     * @return NOUTToken
     */
    public function setWithConfiguration(bool $bWithConfiguration): NOUTToken
    {
        $this->bWithConfiguration = $bWithConfiguration;
        return $this;
    }

    /**
     * @param FormElement|null $oResource
     */
    public function setResource(?FormElement $oResource) : NOUTToken
    {
        $this->oResource = $oResource;
        return $this;
    }

    /**
     * @return FormElement|null
     */
    public function getResource() : ?FormElement
    {
        return $this->oResource;
    }


    /**
     * {@inheritdoc}
     */
    public function __serialize(): array
    {
        return [
            'ip' => $this->sIP,
            'token' => $this->sSessionToken,
            'timezone' => $this->sTimeZone,
            'locale' => $this->sLocale,
            'googleApiKey' => $this->googleApiKey,
            'name' => $this->sNameToDisplay,
            'user' => [
                'class' => get_class($this->oUsernameToken),
                'data' => $this->oUsernameToken->forSerialization()
            ],
            'extranet' => !$this->oExtranetUsernameToken ? null : [
                'class' => get_class($this->oExtranetUsernameToken),
                'data' => $this->oExtranetUsernameToken->forSerialization()
            ],
            'anonyme' => $this->bAnonyme,
            'superviseur' => $this->bSuperviseur,
            'userID' => $this->nIDUser,
            'version' => $this->getVersionNO(),
            'isStarter' => $this->isSIMAXStarter(),
            'language' => is_null($this->clLangage) ? null : $this->clLangage->forSerialization(),
            'resource' => is_null($this->oResource) ? null : $this->oResource->forSerialization(),
            'multilangue' => $this->aMultiLanguage,
            'sessioncodelangue' => $this->nCodeLangue,
            'withconfiguration' => $this->bWithConfiguration,
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

        $this->sIP           = $aUnserialised['ip'];
        $this->sSessionToken = $aUnserialised['token'];
        $this->sTimeZone     = $aUnserialised['timezone'];
        $this->sLocale = $aUnserialised['locale'];

        $this->clLangage = new Langage();
        $this->clLangage->fromSerialization($aUnserialised['language']);
        $this->clVersionNO = new NOUTOnlineVersion($aUnserialised['version']);

        $this->oUsernameToken = new $aUnserialised['user']['class']();
        $this->oUsernameToken->fromSerialization($aUnserialised['user']['data']);

        if (!is_null($aUnserialised['extranet'])){
            $this->oExtranetUsernameToken = new $aUnserialised['extranet']['class']();
            $this->oExtranetUsernameToken->fromSerialization($aUnserialised['extranet']['data']);
        }
        if (!is_null($aUnserialised['resource'])){
            $this->oResource = new FormElement('', '', '', '');
            $this->oResource->fromSerialization($aUnserialised['resource']);
        }
        if(isset($aUnserialised['googleApiKey'])) {
            $this->googleApiKey = $aUnserialised['googleApiKey'];
        }
        if (isset($aUnserialised['anonyme'])){
            $this->bAnonyme = boolval($aUnserialised['anonyme']);
        }
        if (isset($aUnserialised['superviseur'])){
            $this->bSuperviseur = boolval($aUnserialised['superviseur']);
        }
        if (isset($aUnserialised['userID'])){
            $this->nIDUser = $aUnserialised['userID'];
        }
        if (isset($aUnserialised['multilangue'])){
            $this->aMultiLanguage = $aUnserialised['multilangue'];
        }
        if (isset($aUnserialised['sessioncodelangue'])){
            $this->nCodeLangue = $aUnserialised['sessioncodelangue'];
        }
        if (isset($aUnserialised['withconfiguration'])){
            $this->bWithConfiguration = $aUnserialised['withconfiguration'];
        }
        if (isset($aUnserialised['isStarter'])){
            $this->bIsSIMAXStarter = $aUnserialised['isStarter'];
        }

        $this->sNameToDisplay = $aUnserialised['name'];
        parent::__unserialize($aUnserialised['parent_data']);
    }

    const SESSION_LastTimeZone='LastTimeZone';
}
