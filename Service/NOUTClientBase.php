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
use NOUT\Bundle\NOUTOnlineBundle\Entity\UsernameToken\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserXmlXsd;
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
     * @var string
     */
    private $m_sVersionMin;

    /**
     * @var Stopwatch
     */
    private $__stopwatch;

    /**
     * @param OnlineServiceFactory $serviceFactory
     * @param ConfigurationDialogue $configurationDialogue
     * @param NOUTCacheFactory $cacheFactory
     * @param $nVersionDialPref
     * @param Stopwatch|null $stopwatch
     * @param $sVersionMin
     * @param TokenStorageInterface $tokenStorage
     * @throws \Exception
     */
    public function __construct(TokenStorageInterface $tokenStorage,
                                OnlineServiceFactory $serviceFactory,
                                ConfigurationDialogue $configurationDialogue,
                                NOUTCacheFactory $cacheFactory,
                                $sVersionMin,
                                $nVersionDialPref,
                                Stopwatch $stopwatch=null
    )
    {
        $this->__tokenStorage = $tokenStorage;
        $this->__stopwatch = $stopwatch;

        $oSecurityToken = $this->_oGetToken();

        $this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
        $this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

        $this->m_clConfigurationDialogue = $configurationDialogue;
        $this->m_sVersionMin = $sVersionMin;

        //création du gestionnaire de cache
        if ($oSecurityToken instanceof NOUTToken)
        {
            $this->m_clCache = new NOUTClientCache($cacheFactory, $oSecurityToken->getSessionToken(), $oSecurityToken->getLangage());
            $this->m_clRecordSerializer = new RecordSerializer($tokenStorage, $cacheFactory);
        }

        $this->m_clOptionDialogue = new OptionDialogue();
        $this->_initOptionDialogue($nVersionDialPref);
    }

    /**
     * @param $eventName
     */
    private function __startStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->start($eventName);
        }
    }

    /**
     * @param $eventName
     */
    private function __stopStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->stop($eventName);
        }
    }

    /**
     * @param $function
     * @param $plus
     * @return string
     */
    private function _getStopWatchEventName($function, $plus) : string
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
    protected function _oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, string $idForm = null, string $ReturnType = null) : ActionResult
    {
        $clActionResult = new ActionResult($clReponseXML);

        $ReturnType = $ReturnType ?? $clActionResult->ReturnType;
        $this->__startStopwatch($stopWatchEvent = $this->_getStopWatchEventName(__FUNCTION__, $ReturnType));
        switch ($ReturnType)
        {
            case XMLResponseWS::RETURNTYPE_EMPTY:
                break; //on ne fait rien de plus

            case XMLResponseWS::RETURNTYPE_VALUE:

            case XMLResponseWS::RETURNTYPE_XSD:
            case XMLResponseWS::RETURNTYPE_IDENTIFICATION:
            case XMLResponseWS::RETURNTYPE_PLANNING:
            case XMLResponseWS::RETURNTYPE_LISTCALCULATION:
            case XMLResponseWS::RETURNTYPE_EXCEPTION:


            case XMLResponseWS::RETURNTYPE_MAILSERVICESTATUS:
            case XMLResponseWS::RETURNTYPE_WITHAUTOMATICRESPONSE:
            {
                $this->__stopStopwatch($stopWatchEvent);
                throw new \Exception("Type de retour $clActionResult->ReturnType non géré", 1);
            }

            case XMLResponseWS::VIRTUALRETURNTYPE_MAILSERVICERECORD_PJ:
            {
                $clData = $clReponseXML->getFile();
                $clActionResult->setData($clData);
                break;
            }

            case XMLResponseWS::RETURNTYPE_REPORT:
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
                break;
            }

            case XMLResponseWS::RETURNTYPE_MAILSERVICERECORD:
            case XMLResponseWS::RETURNTYPE_VALIDATERECORD:
            case XMLResponseWS::RETURNTYPE_RECORD:
            case XMLResponseWS::RETURNTYPE_VALIDATEACTION:
            {
                // Instance d'un parser
                $clResponseParser = new ReponseWSParser();
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML, $idForm);

                /** @var ParserXmlXsd $clParser */
                if ($clActionResult->ReturnType === XMLResponseWS::RETURNTYPE_MAILSERVICERECORD)
                {
                    /** @var ParserRecord $clParser */
                    $clActionResult->setData($clParser->getFirstRecord());
                }
                else
                {
                    $clActionResult->setData($clParser->getRecord($clReponseXML));
                }
                $clActionResult->setValidateError($clReponseXML->getValidateError());

                break;
            }

            case XMLResponseWS::RETURNTYPE_SCHEDULER:
            {
                // Bug dans InitFromXmlXsd si trop volumineux
                // OutOfMemory\Exception in ParserRecordList.php line 183:
                // Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 262144 bytes)

                $clCount = $clReponseXML->clGetCount();

                // Par sécurité quand on affiche une liste
                if ($clCount->m_nNbDisplay > self::MaxEnregs)
                {
                    //@@@ TODO trad
                    $this->__stopStopwatch($stopWatchEvent);
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

                break;
            }

//            case XMLResponseWS::RETURNTYPE_MAILSERVICELIST:
//            {
//                $idColumn = 'id_' . Langage::COL_MESSAGERIE_IDDossier;
//                $idName = 'id_' . Langage::COL_MESSAGERIE_Libelle;
//                $idParent = 'id_' . Langage::COL_MESSAGERIE_IDDossierPere;
//                $list = new FolderList();
//
//                foreach($clReponseXML->getNodeXML()->children() as $type => $child) {
//                    $id = (string) $child->$idColumn;
//                    $name = (string) $child->$idName;
//                    $parentID = (string) $child->$idParent;
//                    $list->add($id, $name, $parentID);
//                }
//                //var_dump($list);
//                $clActionResult->setData($list);
//                break;
//            }

            case XMLResponseWS::RETURNTYPE_MAILSERVICELIST:
            case XMLResponseWS::RETURNTYPE_GLOBALSEARCH:
            case XMLResponseWS::RETURNTYPE_REQUESTFILTER:
            case XMLResponseWS::RETURNTYPE_THUMBNAIL:
            case XMLResponseWS::RETURNTYPE_DATATREE:
            case XMLResponseWS::RETURNTYPE_LIST:
            {
                // Bug dans InitFromXmlXsd si trop volumineux
                // OutOfMemory\Exception in ParserRecordList.php line 183:
                // Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 262144 bytes)

                $clCount = $clReponseXML->clGetCount();

                // Par sécurité quand on affiche une liste
                if ($clCount->m_nNbDisplay > self::MaxEnregs)
                {
                    //@@@ TODO trad
                    $this->__stopStopwatch($stopWatchEvent);
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

                if ($clActionResult->ReturnType == XMLResponseWS::RETURNTYPE_MAILSERVICELIST){
                    $clFolderCount = $clReponseXML->clGetFolderCount();
                    if ($clFolderCount){
                        $clActionResult->setFolderCount($clFolderCount);
                    }
                }

                break;
            }

            case XMLResponseWS::RETURNTYPE_PRINTTEMPLATE:
            case XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION:
            case XMLResponseWS::RETURNTYPE_CHOICE:
            {

                // Instance d'un parser
                $clResponseParser = new ReponseWSParser();

                /** @var ParserList $clParser */
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                $clSelectorList = new SelectorList($clParser->getList());
                $clActionResult->setData($clSelectorList);
                break;
            }

            /*
            case XMLResponseWS::RETURNTYPE_VALIDATEACTION:
			{
                throw new \Exception("Type de retour RETURNTYPE_VALIDATEACTION non géré", 1);
			}
            */

            case XMLResponseWS::RETURNTYPE_MESSAGEBOX:
            {
                // On fabrique la messageBox avec les données XML
                $clActionResult->setData($clReponseXML->clGetMessageBox());
                break;
            }

            //pour la gestion des graphes
            case XMLResponseWS::RETURNTYPE_NUMBEROFCHART:
            {
                // Instance d'un parser
                $clResponseParser = new ReponseWSParser();

                /** @var ParserNumberOfChart $clParser */
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                $clActionResult
                    ->setData($clParser->getNumberOfChart())
                    ->setValidateError($clReponseXML->getValidateError())
                    ->setCount($clReponseXML->clGetCount());
                break;
            }

            case XMLResponseWS::RETURNTYPE_CHART:
            {
                // Instance d'un parser
                $clResponseParser = new ReponseWSParser();

                /** @var ParserChart $clParser */
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                $clActionResult->setData($clParser->getChart());
                break;
            }

        }

        $this->__stopStopwatch($stopWatchEvent);
        return $clActionResult;
    }

    /**
     * récupère le numéro de version
     * @return NOUTOnlineVersion
     * @throws \Exception
     */
    public function clGetVersion() : NOUTOnlineVersion
    {
        return $this->m_clRESTProxy->clGetVersion();
    }

    /**
     * teste le client pour savoir s'il correspond à la version minimale
     * @return bool
     * @throws \Exception
     */
    public function isVersionMin() : bool
    {
        return $this->clGetVersion()->isVersionSup($this->m_sVersionMin, true);
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

    const PARAMMESS_StartDate   = 'StartDate';
    const PARAMMESS_EndDate     = 'EndDate';
    const PARAMMESS_Filter      = 'Filter';
}