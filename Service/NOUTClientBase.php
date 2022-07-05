<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResultCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging\MailServiceStatus;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserNumberOfChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserRecord;
use NOUT\Bundle\NOUTOnlineBundle\Entity\SelectorList;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\NonceCreatedSecretUsernamePassword;
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserScheduler;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Stopwatch\Stopwatch;


/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
abstract class NOUTClientBase
{
    /**
     * @var ConfigurationDialogue
     */
    private $m_clConfigurationDialogue;

    /**
     * @var SOAPProxy
     */
    protected $m_clSOAPProxy;

    /**
     * @Var RESTProxy
     */
    protected $m_clRESTProxy;


    /**
     * @var TokenStorageInterface
     */
    private $__tokenStorage;

    /**
     * @var NOUTClientCache
     */
    protected $m_clCache = null;

    /**
     * @var RecordSerializer|null
     */
    protected $m_clRecordSerializer = null;

    /**
     * @var OptionDialogue
     */
    private $m_clOptionDialogue;

    /**
     * @var array
     */
    private $m_aVersionMin;

    /**
     * @var Stopwatch
     */
    private $__stopwatch;

    /** @var NOUTOnlineVersion|null  */
    private $m_clNOVersion=null;

    /**
     * @param OnlineServiceFactory  $serviceFactory
     * @param ConfigurationDialogue $configurationDialogue
     * @param NOUTCacheFactory      $cacheFactory
     * @param $nVersionDialPref
     * @param Stopwatch|null        $stopwatch
     * @param array                 $aVersionsMin
     * @param TokenStorageInterface $tokenStorage
     * @throws \Exception
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                OnlineServiceFactory  $serviceFactory,
                                ConfigurationDialogue $configurationDialogue,
                                NOUTCacheFactory      $cacheFactory,
                                array                 $aVersionsMin,
                                                      $nVersionDialPref,
                                Stopwatch             $stopwatch=null
    )
    {
        $this->__tokenStorage = $tokenStorage;
        $this->__stopwatch = $stopwatch;

        $oSecurityToken = $this->_oGetToken();

        $this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
        $this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

        $this->m_clConfigurationDialogue = $configurationDialogue;
        $this->m_aVersionMin = $aVersionsMin;


        //création du gestionnaire de cache
        if ($oSecurityToken instanceof NOUTToken)
        {
            $this->m_clNOVersion = $oSecurityToken->clGetNOUTOnlineVersion();
            $this->m_clCache = new NOUTClientCache($cacheFactory, $oSecurityToken->getSessionToken(), $oSecurityToken->getInfoLangage(), $oSecurityToken->clGetNOUTOnlineVersion());
            $this->m_clRecordSerializer = new RecordSerializer($tokenStorage, $cacheFactory);
        }

        $this->m_clOptionDialogue = new OptionDialogue();
        $this->_initOptionDialogue($nVersionDialPref);
    }

    /**
     * @param $eventName
     */
    protected function __startStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->start($eventName);
        }
    }

    /**
     * @param $eventName
     */
    protected function __stopStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->stop($eventName);
        }
    }

    /**
     * @param $function
     * @param $plus
     * @return string
     */
    protected function _getStopWatchEventName($function, $plus) : string
    {
        return get_class($this).'::'.$function.(empty($plus) ? '' : '::'.$plus);
    }

    /**
     * @return NOUTToken|TokenInterface
     */
    protected function _oGetToken()
    {
        return $this->__tokenStorage->getToken();
    }

    /**
     * @param $sTypeTest
     * @param $sNomParametre
     * @param $sValeurParametre
     * @param $ValeurTest
     * @throws \Exception
     */
    protected function _TestParametre($sTypeTest, $sNomParametre, $sValeurParametre, $ValeurTest)
    {
        switch ($sTypeTest)
        {
            case self::TP_NotEmpty:
                if (empty($sValeurParametre) && $sValeurParametre !== "0")
                {
                    throw new \Exception('the value of the parameter ' . $sNomParametre . ' must not be empty.');
                }
                break;

            case self::TP_InArray:
                if (!in_array($sValeurParametre, $ValeurTest))
                {
                    $sMessage = 'the value of the parameter ' . $sNomParametre . ' must be one of : ';
                    foreach ($ValeurTest as $Value)
                    {
                        $sMessage .= $Value . ', ';
                    }
                    $sMessage = rtrim($sMessage, ", ");
                    $sMessage .= '.';

                    throw new \Exception($sMessage);
                }
                break;
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param string|null   $idForm
     * @param string|null   $ReturnType
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, ?string $idForm=null, ?string $ReturnType=null) : ActionResult
    {
        $clActionResult = new ActionResult($clReponseXML);
        $ReturnType = $ReturnType ?? $clActionResult->ReturnType;
        $this->__startStopwatch($stopWatchEvent = $this->_getStopWatchEventName(__FUNCTION__, $ReturnType));

        $this->__GetActionResultFromXMLResponse($clReponseXML, $clActionResult, $ReturnType, $idForm);

        $this->__stopStopwatch($stopWatchEvent);
        return $clActionResult;
    }

    protected function __GetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, ActionResult $clActionResult, string $ReturnType, ?string $idForm)
    {
        $aPtrFct = array(
            XMLResponseWS::RETURNTYPE_EMPTY => null,

            XMLResponseWS::RETURNTYPE_REPORT => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetReport($clReponseXML, $clActionResult);
            },
            //retour de type fiche
            XMLResponseWS::RETURNTYPE_VALIDATERECORD    => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetRecord($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_VALIDATEACTION    => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetRecord($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_RECORD            => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetRecord($clReponseXML, $clActionResult, $idForm);
            },

            XMLResponseWS::RETURNTYPE_SCHEDULER => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetScheduler($clReponseXML, $clActionResult);
            },
            //retour de type liste
            XMLResponseWS::RETURNTYPE_GLOBALSEARCH    => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetList($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_REQUESTFILTER   => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetList($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_THUMBNAIL       => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetList($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_DATATREE        => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetList($clReponseXML, $clActionResult, $idForm);
            },
            XMLResponseWS::RETURNTYPE_LIST            => function () use ($clReponseXML, $clActionResult, $idForm) {
                $this->_oGetList($clReponseXML, $clActionResult, $idForm);
            },
            //retour de type choix
            XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetSelectorList($clReponseXML, $clActionResult);
            },
            XMLResponseWS::RETURNTYPE_PRINTTEMPLATE     => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetSelectorList($clReponseXML, $clActionResult);
            },
            XMLResponseWS::RETURNTYPE_CHOICE            => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetSelectorList($clReponseXML, $clActionResult);
            },

            //message box
            XMLResponseWS::RETURNTYPE_MESSAGEBOX => function () use ($clReponseXML, $clActionResult) {
                $clActionResult->setData($clReponseXML->clGetMessageBox());
            },

            //les graphes
            XMLResponseWS::RETURNTYPE_CHART         => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetChart($clReponseXML, $clActionResult);
            },
            XMLResponseWS::RETURNTYPE_NUMBEROFCHART => function () use ($clReponseXML, $clActionResult) {
                $this->_oGetNumberOfChart($clReponseXML, $clActionResult);
            },

        );

        if (!array_key_exists($ReturnType, $aPtrFct)){
            throw new \Exception("Type de retour $ReturnType non géré", 1);
        }

        $fct = $aPtrFct[$ReturnType];
        if (!is_null($fct)){
            //on applique la fonction
            $fct(); //pas de paramètre on utilise les use
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @throws \Exception
     */
    private function _oGetReport(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        $clActionResult->setElement($clReponseXML->clGetElement());

        $oNOUTFileInfo  = $clReponseXML->getFile();   // Récupérer éventuellement un fichier
        if (is_null($oNOUTFileInfo))
        {
            // Cas normal
            $clActionResult->setData($clReponseXML->sGetReport());
        }
        else
        {
            $clActionResult->setReturnType(XMLResponseWS::VIRTUALRETURNTYPE_FILE);
            $clActionResult->setData($oNOUTFileInfo);
            if($clActionResult->getAction()->getID() == Langage::ACTION_AfficherFichier_ModeleFichier ||
                $clActionResult->getAction()->getID() == Langage::ACTION_AfficherFichier_NomFichier)
            {
                $clActionResult->setReturnType(XMLResponseWS::VIRTUALRETURNTYPE_FILE_PREVIEW);
            }
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @param string|null        $idForm
     * @throws \Exception
     */
    protected function _oGetRecord(XMLResponseWS $clReponseXML, ActionResult $clActionResult, ?string $idForm)
    {
        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML, $idForm);

        /** @var ParserRecord $clParser */
        $clActionResult->setData($clParser->getRecord($clReponseXML));
        $clActionResult->setValidateError($clReponseXML->getValidateError());
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @param string        $idForm
     * @throws \Exception
     */
    protected function _oGetList(XMLResponseWS $clReponseXML, ActionResult $clActionResult, ?string $idForm)
    {
        // Bug dans InitFromXmlXsd si trop volumineux
        // OutOfMemory\Exception in ParserRecordList.php line 183:
        // Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 262144 bytes)

        $clCount = $clReponseXML->clGetCount();

        // Par sécurité quand on affiche une liste
        if ($clCount->m_nNbDisplay > self::MaxEnregs)
        {
            //@@@ TODO trad
            throw new \Exception("Votre requête a renvoyé trop d'éléments. Contactez l'éditeur du logiciel.", OnlineError::ERR_MEMORY_OVERFLOW);
        }

        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();

        /** @var ParserList $clParser */
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML, $idForm);

        // dump($clParser);
        // clParser est bien du type ParserList mais n'a pas encore les données

        // getList renvoi un RecordList
        $list = $clParser->getList();
        // dump($list);

        $clActionResult
            ->setData($list)
            ->setValidateError($clReponseXML->getValidateError())
            ->setCount($clCount);
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @throws \Exception
     */
    private function _oGetSelectorList(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();

        /** @var ParserList $clParser */
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

        $clSelectorList = new SelectorList($clParser->getList());
        $clActionResult->setData($clSelectorList);
    }


    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @throws \Exception
     */
    private function _oGetChart(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();

        /** @var ParserChart $clParser */
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

        $clActionResult->setData($clParser->getChart());
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @throws \Exception
     */
    private function _oGetNumberOfChart(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();

        /** @var ParserNumberOfChart $clParser */
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

        $clActionResult
            ->setData($clParser->getNumberOfChart())
            ->setValidateError($clReponseXML->getValidateError())
            ->setCount($clReponseXML->clGetCount());
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param ActionResult  $clActionResult
     * @throws \Exception
     */
    private function _oGetScheduler(XMLResponseWS $clReponseXML, ActionResult $clActionResult)
    {
        // Bug dans InitFromXmlXsd si trop volumineux
        // OutOfMemory\Exception in ParserRecordList.php line 183:
        // Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 262144 bytes)

        $clCount = $clReponseXML->clGetCount();

        // Par sécurité quand on affiche une liste
        if ($clCount->m_nNbDisplay > self::MaxEnregs)
        {
            //@@@ TODO trad
            throw new \Exception("Votre requête a renvoyé trop d'éléments. Contactez l'éditeur du logiciel.", OnlineError::ERR_MEMORY_OVERFLOW);
        }

        // Instance d'un parser
        $clResponseParser = new ReponseWSParser();

        /** @var ParserScheduler $clParser */
        $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

        // dump($clParser);
        // clParser est bien du type ParserList mais n'a pas encore les données

        $list   = $clParser->getList();
        $users  = $clParser->getScheduler(); // Les utilisateurs pour un planning partagé

        $clActionResult
            ->setData($list) //le pas écraser list sinon on perd les boutons
            ->setExtraData($users)
            ->setValidateError($clReponseXML->getValidateError())
            ->setCount($clReponseXML->clGetCount());
    }


    /**
     * récupère le numéro de version
     * @return NOUTOnlineVersion
     * @throws \Exception
     */
    public function clGetVersion() : NOUTOnlineVersion
    {
        if ($this->m_clNOVersion instanceof NOUTOnlineVersion){
            return $this->m_clNOVersion;
        }

        return $this->m_clRESTProxy->clGetVersion();
    }

    /**
     * teste le client pour savoir s'il correspond à la version minimale
     * @return bool
     * @throws \Exception
     */
    public function isVersionMinSite() : bool
    {
        return $this->clGetVersion()->isVersionSup($this->m_aVersionMin['site'], true);
    }

    public function bGereWSDL($opt) : bool
    {
        switch($opt)
        {
            case self::OPT_MenuVisible:
            {
                return $this->clGetVersion()->isVersionSup('1550.01', true);
            }
        }
        return false;
    }

    /**
     * teste le client pour savoir s'il correspond à la version minimale
     * @return bool
     * @throws \Exception
     */
    public function isVersionMinLanguage() : bool
    {
        return $this->clGetVersion()->isVersionSup($this->m_aVersionMin['language'], true);
    }

    /**
     * @return NOUTCacheProvider
     */
    public function getCacheSession() : ?NOUTCacheProvider
    {
        if (!is_null($this->m_clCache))
        {
            return $this->m_clCache->getCacheSession();
        }

        return null;
    }

    public function getCacheLanguage() : ?NOUTCacheProvider
    {
        if (!is_null($this->m_clCache))
        {
            return $this->m_clCache->getCacheLanguage();
        }

        return null;
    }

    /**
     * @param $cache
     * @param $name
     * @return mixed
     */
    public function fetchFromCache($cache, $name)
    {
        if (!is_null($this->m_clCache))
        {
            return $this->m_clCache->fetch($cache, $name);
        }

        return null;
    }

    /**
     * @param $cache
     * @param $name
     * @param $data
     * @return mixed|null
     */
    protected function _saveInCache($cache, $name, $data)
    {
        if (!is_null($this->m_clCache))
        {
            return $this->m_clCache->save($cache, $name, $data);
        }

        return null;
    }

    /**
     * @return string
     */
    public function getTimeZone() : string
    {
        return $this->_oGetToken()->getTimeZone();
    }

    /**
     * initialise les options de dialogue
     * @param $nVersionPref
     */
    protected function _initOptionDialogue($nVersionPref)
    {
        $this->m_clOptionDialogue->InitDefault($nVersionPref);
        $this->m_clOptionDialogue->DisplayValue = OptionDialogue::DISPLAY_None;
        $this->m_clOptionDialogue->LanguageCode = $this->m_clConfigurationDialogue->getLangCode();
    }

    /**
     * Ne pas renommer ou supprimer, la méthode est utilisé par _aGetHeaderSuppl
     * @param $property
     * @param $value
     */
    protected function setOptionDialogueProperty($property, $value)
    {
        if (property_exists($this->m_clOptionDialogue, $property)){
            $this->m_clOptionDialogue->$property = $value;
        }
    }

    /**
     * retourne les options de dialogue
     * @return OptionDialogue
     */
    protected function _clGetOptionDialogue() : OptionDialogue
    {
        return $this->m_clOptionDialogue;
    }

    /**
     * @param NOUTToken $oToken
     * @return UsernameToken|null
     * @throws \Exception
     */
    protected function _oGetUsernameToken(NOUTToken $oToken) :?UsernameToken
    {
        return $oToken->getUsernameToken();
    }

    /**
     * @return NonceCreatedSecretUsernamePassword
     */
    protected function _oGetNCSUsernameToken() : NonceCreatedSecretUsernamePassword
    {
        return new NonceCreatedSecretUsernamePassword($this->m_clConfigurationDialogue->getSecret());
    }

    /**
     * @param string $sIDContexteAction
     * @param bool $bAPIUser
     * @return Identification
     * @throws \Exception
     */
    protected function _clGetIdentificationREST(string $sIDContexteAction, bool $bAPIUser) : Identification
    {
        $clIdentification = new Identification();

        // récupération de l'utilisateur connecté
        $oToken = $this->_oGetToken();

        $clIdentification->m_clUsernameToken = $this->_oGetUsernameToken($oToken);
        $clIdentification->m_sTokenSession = $oToken->getSessionToken();
        $clIdentification->m_sIDContexteAction = $sIDContexteAction;
        $clIdentification->m_bAPIUser = $bAPIUser;

        return $clIdentification;
    }

    /**
     * @param array|null $aHeaderSuppl
     * @return array
     * @throws \Exception
     */
    protected function _aGetTabHeader(array $aHeaderSuppl = null) : array
    {
        // récupération de l'utilisateur connecté
        $oToken = $this->_oGetToken();

        // Headers par défaut
        $aTabHeader = array(
            SOAPProxy::HEADER_UsernameToken => $this->_oGetUsernameToken($oToken),
            SOAPProxy::HEADER_SessionToken => $oToken->getSessionToken(),
            SOAPProxy::HEADER_OptionDialogue => $this->_clGetOptionDialogue(),
        );

        // Headers supplémentaires
        if (!empty($aHeaderSuppl))
        {
            $aTabHeader = array_merge($aTabHeader, $aHeaderSuppl);
        }

        return $aTabHeader;
    }

    /**
     * initialise la structure du header a partir du tableau du header de la requête HTTP
     * @param array|null  $aTabHeaderQuery
     * @param string|null $idcontexte
     * @param int|null    $autovalidate
     * @return array
     */
    protected function _aGetHeaderSuppl(?array $aTabHeaderQuery, string $idcontexte=null, int $autovalidate=null) : array
    {
        $aTabHeaderSuppl = array();

        if (is_array($aTabHeaderQuery))
        {
            // $oParam du type OptionDialogue
            foreach ($aTabHeaderQuery as $property => $value)
            {
                if(is_array($value)) // On a une propriété de second niveau (par exemple OptionDialogue)
                {
                    $setFunctionName = "set" . $property . "Property";

                    if(method_exists($this, $setFunctionName))
                    {
                        foreach ($value as $optionProperty => $optionValue)
                        {
                            $this->$setFunctionName($optionProperty, $optionValue);
                        }
                    }
                }
                elseif(!is_object($value)) // Propriété de premier niveau (scalar)
                {
                    if(SOAPProxy::s_isValidHeaderProp($property))
                    {
                        $aTabHeaderSuppl[$property] = $value;
                    }
                }
            }
        }

        if (!empty($idcontexte)){
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $idcontexte;
        }
        if (!empty($autovalidate)){
            $aTabHeaderSuppl[SOAPProxy::HEADER_AutoValidate] = $autovalidate;
        }
        return $aTabHeaderSuppl;
    }

    /**
     * initialise la structure de paramètre a partir du tableau des paramètres de la requête HTTP
     * @param string $classname
     * @param array $aTabParamRequest
     * @return mixed
     */
    protected function _oGetParam(string $classname, array $aTabParamRequest)
    {
        $oParam = new $classname();
        foreach ($aTabParamRequest as $property => $valeur)
        {
            if (property_exists($oParam, $property))
            {
                $oParam->$property = $valeur;
            }
        }

        if (property_exists($oParam, self::PARAM_SPECIALPARAMLIST) && is_null($oParam->{self::PARAM_SPECIALPARAMLIST}))
        {
            $oParam->{self::PARAM_SPECIALPARAMLIST} = new SpecialParamListType();
            $oParam->{self::PARAM_SPECIALPARAMLIST}->initFirstLength();
        }

        if (property_exists($oParam, self::PARAM_PARAMXML) && is_null($oParam->{self::PARAM_PARAMXML}))
        {
            $oParam->{self::PARAM_PARAMXML} = '';
        }
        return $oParam;
    }

    /**
     * @param NOUTFileInfo $oRet
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oMakeResultFromFile(NOUTFileInfo $oRet) : ActionResult
    {
        $clActionResult = new ActionResult(null);
        $clActionResult->setData($oRet);

        //gestion du cache de symfony
        $clActionResult->setTypeCache($oRet->isNoCache() ? ActionResultCache::TYPECACHE_None : ActionResultCache::TYPECACHE_Private);
        $clActionResult->setLastModified($oRet->getDTLastModified());

        return $clActionResult;
    }

    /**
     * récupère un fichier
     * @param string $idcontexte
     * @param string $idihm
     * @param string $idForm
     * @param string $idColumn
     * @param string $idRecord
     * @param array  $aTabOptions
     * @return ActionResult
     * @throws \Exception
     */
    public function getFile(string $idcontexte, string $idihm, string $idForm, string $idColumn, string $idRecord, array $aTabOptions) : ActionResult
    {
        $oHTTPResponse = $this->_getFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, $aTabOptions);
        return $this->_oMakeResultFromFile($oHTTPResponse);
    }


    /**
     * récupère un fichier pour téléchargement
     * @param string $idcontexte
     * @param string $idihm
     * @param string $idForm
     * @param string $idColumn
     * @param string $idRecord
     * @param array  $aTabOptions
     * @return false|NOUTFileInfo
     * @throws \Exception
     */
    private function _getFile(string $idcontexte, string $idihm, string $idForm, string $idColumn, string $idRecord, array $aTabOptions)
    {
        $clIdentification = $this->_clGetIdentificationREST($idcontexte, false);

        //on veut le contenu
        $aTabOptions[RESTProxy::OPTION_WantContent] = 1;

        if (!is_null($this->m_clCache)){
            $dataCache = $this->m_clCache->fetchFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, $aTabOptions);
            if (isset($dataCache) && ($dataCache !== false)){
                return $dataCache;
            }
        }

        //on a pas l'image en cache avec les options en question, il faut la récupérer
        /** @var NOUTFileInfo $oFileInRecord */
        $oNOUTFileInfo  = $this->m_clRESTProxy->oGetFileInRecord(
            $idForm,
            $idRecord,
            $idColumn,
            array(),
            $aTabOptions,
            $clIdentification
        );

        if (!is_null($this->m_clCache)){
            $this->m_clCache->saveFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, $aTabOptions, $oNOUTFileInfo);
        }

        return $oNOUTFileInfo;
    }

    /**
     * @param UploadedFile $file
     * @param string       $idcontexte
     * @param string       $idihm
     * @param string       $idcolonne
     * @return ActionResult
     */
    public function saveUploadFileInCache(UploadedFile $file, string $idcontexte, string $idihm, string $idcolonne) : ActionResult
    {
        $data = new NOUTFileInfo();
        $data->initFromUploadedFile($file);
        return $this->saveNOUTFileInCache($data, $idcontexte, $idihm, $idcolonne);
    }

    /**
     * @param string $idcontexte
     * @param string $idihm
     * @param string $idcolonne
     * @param string $dataBase64
     * @param string $mimetype
     * @return ActionResult
     */
    public function saveBase64DataInCache(string $dataBase64, string $mimetype, string $idcontexte, string $idihm, string $idcolonne) : ActionResult
    {
//        $temp_file = tempnam(sys_get_temp_dir(), 'drawing');
//        file_put_contents($temp_file, base64_decode($dataBase64));

        $data = new NOUTFileInfo();
        $data->initImgFromUploadedBase64Data($dataBase64, $mimetype, $idcolonne);
        return $this->saveNOUTFileInCache($data, $idcontexte, $idihm, $idcolonne);
    }


    /**
     * @param NOUTFileInfo $file
     * @param string       $idcontexte
     * @param string       $idihm
     * @param string       $idcolonne
     * @return ActionResult
     */
    public function saveNOUTFileInCache(NOUTFileInfo $file, string $idcontexte, string $idihm, string $idcolonne) : ActionResult
    {
        $name = $this->m_clCache->saveFile($idcontexte, $idihm, '', $idcolonne, '', array(), $file);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($name);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);

        return $clActionResult;
    }


    /**
     * @param string $idcontexte
     * @param string $idihm
     * @param string $name
     * @return ActionResult
     */
    public function getFileInCache(string $idcontexte, string $idihm, string $name) : ActionResult
    {
        $data = $this->m_clCache->fetchFileFromName($idcontexte, $idihm, $name);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($data);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);
        // $clActionResult->setLastModified(new \DateTime('@'.filemtime($filePath))); // Erreur si le fichier n'existe pas

        return $clActionResult;
    }

    // Fin Fichiers
    // ------------------------------------------------------------------------------------

    const TP_NotEmpty = 1;
    const TP_InArray = 2;
    const MaxEnregs = 200;    // Nombre maximum d'éléments sur une page

    const PARAM_SPECIALPARAMLIST    = 'SpecialParamList';
    const PARAM_PARAMXML            = 'ParamXML';
    const PARAM_CALLINGCOLUMN       = 'CallingColumn';
    const PARAM_DISPLAYMODE         = 'DisplayMode';
    const PARAM_CHECKSUM            = 'Checksum';
    const PARAM_CALLINGINFO         = 'CallingInfo';
    const PARAM_BTN_LISTMODE        = 'BtnListMode';

    const OPT_MenuVisible = 'menu_visible';
}