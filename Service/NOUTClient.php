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
use NOUT\Bundle\NOUTOnlineBundle\Entity\IHMLoader;
use NOUT\Bundle\NOUTOnlineBundle\Entity\InfoIHM;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Menu\ItemMenu;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ParametersManagement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConnexionExtranetHashPassword;
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
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondColumn;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondValue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType\CondListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Factory\CondListTypeFactory;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserXmlXsd;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserScheduler;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\GestionWSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\AddPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ButtonAction;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateFrom;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DataPJType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DataType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DeletePJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Export;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetContentFolder;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPJ;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetSubListContent;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Import;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Merge;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ModifyMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectChoice;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SendMessage;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderSubList;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateColumnMessageValueInBatch;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateFilter;
use NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token\NOUTToken;
use NOUT\Bundle\NOUTOnlineBundle\NOUTException\NOUTValidationException;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\UpdateMessage;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Stopwatch\Stopwatch;


/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
class NOUTClient
{
    /**
     * @var ConfigurationDialogue
     */
    private $m_clConfigurationDialogue;

    /**
     * @var SOAPProxy
     */
    private $m_clSOAPProxy;

    /**
     * @Var RESTProxy
     */
    private $m_clRESTProxy;


    /**
     * @var TokenStorageInterface
     */
    private $__tokenStorage;

    /**
     * @var NOUTClientCache
     */
    private $m_clCache = null;

    /**
     * @var RecordSerializer|null
     */
    private $m_clRecordSerializer = null;

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
                if ($clCount->m_nNbDisplay > NOUTClient::MaxEnregs)
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
                if ($clCount->m_nNbDisplay > NOUTClient::MaxEnregs)
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
     * @param string           $sIDform        identifiant du formulaire
     * @param ConditionFileNPI $clFileNPI      condition pour la requête
     * @param array            $TabColonneAff  tableau des colonnes a afficher
     * @param array|null       $TabHeaderSuppl tableau des headers
     * @return XMLResponseWS
     *@throws \Exception
     */
    protected function _oRequest(string $sIDform, ConditionFileNPI $clFileNPI, array $TabColonneAff, ?array $TabHeaderSuppl=null) : XMLResponseWS
    {
        $clParamRequest = new Request();
        $clParamRequest->Table = $sIDform;
        $clParamRequest->CondList = $clFileNPI->sToSoap();
        $clParamRequest->ColList = new ColListType($TabColonneAff);

        return $this->m_clSOAPProxy->request($clParamRequest, $this->_aGetTabHeader($TabHeaderSuppl));
    }

    /**
     * @param string       $table
     * @param CondListType $condList
     * @param array        $colList
     * @param array|null   $tabHeaderSuppl
     * @return XMLResponseWS
     * @throws \Exception
     */
    protected function _oNewRequest(string $table, CondListType $condList, array $colList, ?array $tabHeaderSuppl=null) : XMLResponseWS
    {
        $clParamRequest = new Request();
        $clParamRequest->ColList = new ColListType($colList);
        $clParamRequest->Table = $table;
        $clParamRequest->CondList = $condList;
        $clParamRequest->MaxResult = self::MaxEnregs;
        return $this->m_clSOAPProxy->request($clParamRequest, $this->_aGetTabHeader($tabHeaderSuppl));
    }


    /**
     * récupère la liste des icônes avec une grosse image
     * @param string $idCol
     * @return XMLResponseWS
     * @throws \Exception
     */
    protected function _oGetTabIcon(string $idCol) : XMLResponseWS
    {
        $aTabColonne = array();

        $clFileNPI = new ConditionFileNPI();
        $clFileNPI->EmpileCondition($idCol, CondType::COND_DIFFERENT, '');


        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel,  //on ne garde pas le contexte ouvert
        );

        return $this->_oRequest(Langage::TABL_ImageCatalogue, $clFileNPI, $aTabColonne, $aTabHeaderSuppl);
    }


    /**
     * récupère la liste des options de menu sur les actions accordées par les droits et les séparateurs
     * @return XMLResponseWS
     * @throws \Exception
     */
    protected function _oGetTabMenu_OptionMenu() : XMLResponseWS
    {
        $aTabColonne = array(
            Langage::COL_OPTIONMENUPOURTOUS_IDAction,
            Langage::COL_OPTIONMENUPOURTOUS_IDOptionMenu,
            Langage::COL_OPTIONMENUPOURTOUS_Libelle,
            Langage::COL_OPTIONMENUPOURTOUS_Commande,
            Langage::COL_OPTIONMENUPOURTOUS_IDIcone,
            Langage::COL_OPTIONMENUPOURTOUS_IDMenuParent,
        );

        $clFileNPI = new ConditionFileNPI();

        //les options de menu qui servent de séparateur
        $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDAction, CondType::COND_EQUAL, '');
        $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_Libelle, CondType::COND_EQUAL, '-');
        $clFileNPI->EmpileOperateur(Operator::OP_AND);
        $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_Commande, CondType::COND_EQUAL, '');
        $clFileNPI->EmpileOperateur(Operator::OP_AND);

        //les options de menu sur lesquelles les droits sont accordés
        if ($this->m_clSOAPProxy->getGestionWSDL()->bGere(GestionWSDL::OPT_MenuVisible))
        {
            $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDOptionMenu, CondType::COND_MENUVISIBLE, 1);
        } else
        {
            $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDAction, CondType::COND_WITHRIGHT, 1);
        }
        $clFileNPI->EmpileOperateur(Operator::OP_OR);
        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel,  //on ne garde pas le contexte ouvert
        );

        return $this->_oRequest(Langage::TABL_OptionMenuPourTous, $clFileNPI, $aTabColonne, $aTabHeaderSuppl);
    }


    /**
     * récupère la liste des menus
     * @return XMLResponseWS
     * @throws \Exception
     */
    protected function _oGetTabMenu_Menu() : XMLResponseWS
    {
        $aTabColonne = array(
            Langage::COL_MENUPOURTOUS_OptionsMenu,
            Langage::COL_MENUPOURTOUS_IDMenuParent,
            Langage::COL_MENUPOURTOUS_Libelle,
            Langage::COL_MENUPOURTOUS_Ordre,
        );

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel, //on ne garde pas le contexte ouvert
            SOAPProxy::HEADER_APIUser => SOAPProxy::APIUSER_Active,           //on force l'utilisation de l'user d'application (max) car un utilisateur classique n'aura pas les droit d'exécuter cette requête
        );

        return $this->_oRequest(Langage::TABL_MenuPourTous, new ConditionFileNPI(), $aTabColonne, $aTabHeaderSuppl);
    }

    /**
     * récupère les infos du menu,
     * c'est sauvé dans le cache de session à cause de la Formule Visible des menu et option de menu
     *  comme on peut avoir n'importe quoi dans la formule, cela ne peut pas être lié au paramétrage
     *
     * @return mixed|InfoIHM|null
     * @throws \Exception
     */
    protected function _oGetInfoIhmMenu()
    {
        $sUsername = $this->_oGetToken()->getUsername();
        $oInfoIHM = $this->fetchFromCache(NOUTClientCache::CACHE_Session, "info_$sUsername");
        if (isset($oInfoIHM) && ($oInfoIHM !== false)){
            return $oInfoIHM; //on a déjà les infos du menu
        }

        //on a pas les infos, il faut les calculer
        $clReponseXML_OptionMenu = $this->_oGetTabMenu_OptionMenu();
        $clReponseXML_Menu = $this->_oGetTabMenu_Menu();
        $clReponseXML_SmallIcon = $this->_oGetTabIcon(Langage::COL_IMAGECATALOGUE_Image);
        $clReponseXML_BigIcon = $this->_oGetTabIcon(Langage::COL_IMAGECATALOGUE_ImageGrande);

        $clIHMLoader = new IHMLoader($clReponseXML_OptionMenu, $clReponseXML_Menu, $clReponseXML_SmallIcon, $clReponseXML_BigIcon);
        $oInfoIHM = $clIHMLoader->oGetInfoIHM();

        $this->_saveInCache(NOUTClientCache::CACHE_Session, "info_$sUsername", $oInfoIHM);
        return $oInfoIHM;
    }

    /**
     * @param string $idContext
     * @return string
     * @throws \Exception
     */
    public function getHelp(string $idContext) : string
    {
        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        return $this->m_clRESTProxy->oGetHelp($clIdentification)->content;
    }

    /**
     * récupère les infos d'ihm lié au menu (menu, toolbar, et icône centraux)
     * c'est sauvé dans le cache de session à cause de la Formule Visible des menu et option de menu
     *  comme on peut avoir n'importe quoi dans la formule, cela ne peut pas être lié au paramétrage
     *
     * @param string $method
     * @param string $prefix
     * @return array|mixed|null
     * @throws \Exception
     */
    protected function __oGetIhmMenuPart(string $method, string $prefix)
    {
        $sUsername = $this->_oGetToken()->getUsername();
        $aTabMenu = $this->fetchFromCache(NOUTClientCache::CACHE_Session, "info_{$prefix}_$sUsername");
        if (isset($aTabMenu) && ($aTabMenu !== false)){
            return $aTabMenu; //on a déjà les infos du menu
        }

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aInfo = array();
        //on a pas les infos, il faut les calculer
        /** @var HTTPResponse $oRetHTTP */
        $oRetHTTP = $this->m_clRESTProxy->$method($clIdentification);
        $json = json_decode($oRetHTTP->content, false, 512, JSON_BIGINT_AS_STRING);
        if (is_array($json) && (count($json) > 0))
        {
            foreach ($json as $objet)
            {
                //TODO: Remove when annuler/refaire/recherche globale is implemented
                if(
                    $objet->idaction != Langage::ACTION_Annulation &&
                    $objet->idaction != Langage::ACTION_Refaire &&
                    $objet->idaction != Langage::ACTION_RechercheGlobale
                ) {
                    $itemMenu = $this->__oGetItemMenu($objet);
                    $aInfo[] = $itemMenu;
                }
            }

            if ($prefix=='menu'){
                //c'est le menu, il faut vérifier le a plat
                if (count($aInfo)==1)
                {
                    $tabOptions = $aInfo[0]->tabOptions;
                    $aInfo = array();
                    foreach($tabOptions as $clMenu)
                    {
                        /** @var ItemMenu $clMenu */
                        if (!$clMenu->isSeparator()){
                            $aInfo[]=$clMenu;
                        }
                    }
                }
            }
        }

        if (!json_last_error()){
            $this->_saveInCache(NOUTClientCache::CACHE_Session, "info_{$prefix}_$sUsername", $aInfo);
        }
        return $aInfo;
    }

    /**
     * récupère les infos du menu
     * @param \stdClass $objSrc
     * @return ItemMenu
     */
    protected function __oGetItemMenu(\stdClass $objSrc) : ItemMenu
    {
        $itemMenu = new ItemMenu($objSrc->id, $objSrc->title, boolval($objSrc->is_menu_option));
        $itemMenu
            ->setSeparator(boolval($objSrc->is_separator))
            ->setRootMenu(boolval($objSrc->is_root))
            ->setIdAction($objSrc->idaction)
            ->setCommand($objSrc->command)
            ->setIconBig($objSrc->icon_big)
            ->setIconSmall($objSrc->icon_small)
            ->setHomeWithImg(boolval($objSrc->home_withimg))
            ->setHomeDesc($objSrc->home_desc)
            ->setHomeTitle($objSrc->home_title)
            ->setHomeWidth(intval($objSrc->home_width))
            ->setHomeHeight(intval($objSrc->home_height));

        if (count($objSrc->tab_options) > 0)
        {
            foreach ($objSrc->tab_options as $objChild)
            {
                //TODO: Remove when annuler/refaire is implemented
                if(
                    $objChild->idaction != Langage::ACTION_Annulation &&
                    $objChild->idaction != Langage::ACTION_Refaire &&
                    $objChild->idaction != Langage::ACTION_RechercheGlobale
                ) {
                    $childMenu = $this->__oGetItemMenu($objChild);
                    $itemMenu->AddOptionMenu($childMenu);
                }
            }
        }

        return $itemMenu;
    }

    /**
     * @param string $member_name
     * @param string $method_name
     * @param string $prefix
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oGetIhmMenuPart(string $member_name, string $method_name, string $prefix) : ActionResult
    {
        $clActionResult = new ActionResult(null);
        if (!$this->_oGetToken()->isVersionSup('1637.02', false))
        {
            //l'ancien système
            $oInfoMenu = $this->_oGetInfoIhmMenu();
            $clActionResult->setData($oInfoMenu->$member_name);
        }
        else
        {
            $tabMenu = $this->__oGetIhmMenuPart($method_name, $prefix);
            $clActionResult->setData($tabMenu);
        }

        //le menu dépend de l'utilisateur, c'est un cache privé
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);
        return $clActionResult;

    }

    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     * @throws \Exception
     */
    public function getTabMenu() : ActionResult
    {
        return $this->_oGetIhmMenuPart('aMenu', 'oGetMenu', 'menu');
    }


    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     * @throws \Exception
     */
    public function getCentralIcon(): ActionResult
    {
        return $this->_oGetIhmMenuPart('aBigIcon', 'oGetCentralIcon', 'home');
    }

    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     * @throws \Exception
     */
    public function getToolbar(): ActionResult
    {
        return $this->_oGetIhmMenuPart('aToolbar', 'oGetToolbar', 'toolbar');
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
     * Execute une action via son id
     * @param array      $tabParamQuery
     * @param array|null $aTabHeaderQuery
     * @param string     $sIDAction
     * @param int        $final
     * @param string     $sIDContext
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecIDAction(string $sIDAction, string $sIDContext, array $tabParamQuery, ?array $aTabHeaderQuery = null, int $final = 0) : ActionResult
    {
        // Les paramètres du header sont passés par array

        //--------------------------------------------------------------------------------------------
        // Paramètres
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->ID = $sIDAction;             // identifiant de l'action (String)
        $clParam->Final = $final;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContext);

        //--------------------------------------------------------------------------------------------
        // L'action
        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param array $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecute(array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * Execute une action via sa phrase
     * @param array      $tabParamQuery
     * @param string     $sPhrase
     * @param string     $sIDContexte
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecSentence(string $sPhrase, string $sIDContexte, array $tabParamQuery, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->Sentence = $sPhrase;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContexte);

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param string $sLoginExtranet
     * @param string $sPassword
     * @param string $sTypeEncodage
     * @param int    $codeLangue
     * @param string $sLoginSIMAX
     * @param string $sPassworSIMAX
     * @param string $sFormulaireExtranet
     * @param bool   $bFromLogin
     * @return ActionResult
     * @throws \Exception
     */
    public function oConnexionExtranet(string $sLoginExtranet, string $sPassword, string $sTypeEncodage, int $codeLangue, string $sLoginSIMAX, string $sPassworSIMAX, string $sFormulaireExtranet, bool $bFromLogin) : ActionResult
    {
        $clParam = new Execute();
        $clParam->ID = Langage::ACTION_ConnexionExtranet;

        //il faut encoder le mot de passe simax
        $sEncodedSIMAX = ConnexionExtranetHashPassword::s_sHashPasswordSIMAX($sPassworSIMAX);
        //et le mot de passe extranet
        $sEncodedExtranet = ConnexionExtranetHashPassword::s_sHashPassword($sPassword, $sTypeEncodage);

        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([
            Langage::PA_ConnexionExtranet_Extranet_Pseudo => $sLoginExtranet,
            Langage::PA_ConnexionExtranet_Extranet_Mdp    => $sEncodedExtranet,
            Langage::PA_ConnexionExtranet_Intranet_Pseudo => $sLoginSIMAX,
            Langage::PA_ConnexionExtranet_Intranet_Mdp    => $sEncodedSIMAX,
            Langage::PA_ConnexionExtranet_Formulaire      => $sFormulaireExtranet,
            Langage::PA_ConnexionExtranet_CodeLangue      => $codeLangue,
            Langage::PA_ConnexionExtranet_FromLogin       => $bFromLogin ? 1 : 0,
        ]);

        $oRet = $this->_oExecute($clParam, []);

        //ici il faut invalider le cache
        //$this->m_clCache
        return $oRet;
    }

    /**
     * @param array      $tabParamQuery
     * @param string     $sIDTableau
     * @param string     $sIDContexte
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecList(array $tabParamQuery, string $sIDTableau, string $sIDContexte = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(ListParams::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->listAction($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecListRequest(string $tableID, string $contextID = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Requete, Langage::COL_REQUETE_IDTableau, [], $aTabHeaderQuery);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param string     $requestTableId
     * @param string     $requestColId
     * @param array      $colList
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oExecRequestOnIDTableau(string $tableID, string $contextID, string $requestTableId, string $requestColId, array $colList, ?array $aTabHeaderQuery=null) : ActionResult
    {
        $condition = new Condition(
            new CondColumn($requestColId),
            new CondType(CondType::COND_EQUAL),
            new CondValue($tableID));
        $condList = CondListTypeFactory::create($condition);

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $clReponseXML = $this->_oNewRequest(
            $requestTableId,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecListCalculation(string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $clReponseXML = $this->m_clSOAPProxy->getEndListCalculation($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetDefaultExportAction(string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //----------------------------------------------------------------------------------
        $aTabColonne = array();
        $default_export_action = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_EQUAL),
            new CondValue(Langage::ACTION_Export)
        );
        $has_rights = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_WITHRIGHT),
            new CondValue('1')
        );

        $operator = new Operator(Operator::OP_AND);
        $operator->addCondition($default_export_action)
            ->addCondition($has_rights);

        $condList = CondListTypeFactory::create($operator);

        $clReponseXML = $this->_oNewRequest(Langage::TABL_Action,
            $condList,
            $aTabColonne,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetExportsList(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Export, Langage::COL_EXPORT_IDTableau, [Langage::COL_EXPORT_Libelle], $aTabHeaderQuery);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetImportsList(string $tableID, string $contextID) : ActionResult
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Import, Langage::COL_IMPORT_Formulaire, [Langage::COL_IMPORT_Libelle]);
    }

    /**
     * @param string     $contextID
     * @param string     $tableId
     * @param string     $actionId
     * @param string     $exportId
     * @param string     $format
     * @param string     $module
     * @param string     $colType
     * @param string     $items
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExport(string $contextID, string $tableId, string $actionId, string $exportId, string $format, string $module, string $colType, string $items, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        $export = new Export();
        $export->Table = $tableId;
        $export->ID = $actionId;
        $export->Export = $exportId;
        $export->Format = $format;
        $export->Module = $module;
        $export->ColType = $colType;
        $export->items = $items;

        $clReponseXML = $this->m_clSOAPProxy->export($export, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string            $tableId
     * @param string            $actionId
     * @param string            $importId
     * @param UploadedFile|null $file
     * @param array|null        $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oImport(string $tableId, string $actionId, string $importId, ?UploadedFile $file = null, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery);

        //------------------------------------------------------------------------------------------------------
        $data = null;

        if($file instanceof UploadedFile) {
            $encoding = 'base64';
            $filename = $file->getClientOriginalName();
            $size = $file->getSize();
            $fileData = base64_encode(stream_get_contents(fopen($file->getRealPath(), 'rb')));

            $data = new DataType();
            $data->filename = $filename;
            $data->encoding = $encoding;
            $data->size = $size;
            $data->_ = $fileData;
        }
        $import = new Import();

        $import->Table = $tableId;
        $import->ID = $actionId;
        $import->Import = $importId;
        $import->File = $data;

        $clReponseXML = $this->m_clSOAPProxy->import($import, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param string     $eTypeAction
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oRequestImportExportActions(string $tableID, string $contextID, string $eTypeAction, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //--------------------------------------------------------------------------------------------
        $colList = array(Langage::COL_ACTION_Libelle);
        $table_actions = new Condition(
            new CondColumn(Langage::COL_ACTION_IDTableau),
            new CondType(CondType::COND_EQUAL),
            new CondValue($tableID)
        );
        $has_rights = new Condition(
            new CondColumn(Langage::COL_ACTION_IDAction),
            new CondType(CondType::COND_WITHRIGHT),
            new CondValue(1)
        );
        $type_actions = new Condition(
            new CondColumn(Langage::COL_ACTION_TypeAction),
            new CondType(CondType::COND_EQUAL),
            new CondValue($eTypeAction)
        );
        $operator = new Operator(Operator::OP_AND);
        $operator->addCondition($table_actions)
            ->addCondition($has_rights)
            ->addCondition($type_actions);

        $condList = CondListTypeFactory::create($operator);

        //----------------------------------
        $clReponseXML = $this->_oNewRequest(
            Langage::TABL_Action,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetExportsActions(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Exporter, $aTabHeaderQuery);
    }

    /**
     * @param string     $tableID
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetImportsActions(string $tableID, string $contextID, ?array $aTabHeaderQuery=null) : ActionResult
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Importer, $aTabHeaderQuery);
    }

    /**
     * Affichage d'une liste via l'action recherche
     * @param array      $tabParamQuery
     * @param string     $sIDTableau
     * @param string     $contextID
     * @param array|null $aTabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecSearch(array $tabParamQuery, string $sIDTableau, string $contextID = '', ?array $aTabHeaderQuery=null) : ActionResult
    {
        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $contextID);

        //-----------------------------
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(Search::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        $clReponseXML = $this->m_clSOAPProxy->search($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param Execute $clParamExecute
     * @param array $aTabHeaderSuppl
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oExecute(Execute $clParamExecute, array $aTabHeaderSuppl) : ActionResult
    {
        $clReponseXML = $this->m_clSOAPProxy->execute($clParamExecute, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Execute une action via son id
     * @param array      $tabParamQuery
     * @param string     $idcolonne
     * @param Record     $clRecord
     * @param array|null $aTabHeaderQuery
     * @param string     $idcontexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetSublistContent(Record $clRecord, string $idcolonne, string $idcontexte, array $tabParamQuery, ?array $aTabHeaderQuery=null) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$idColumn', $idcolonne, null);

        //paramètre de l'action liste
        $clParam = $this->_oGetParam(GetSubListContent::class, $tabParamQuery);
        $clParam->Record = $clRecord->getIDEnreg();
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($aTabHeaderQuery, $idcontexte);

        $clReponseXML = $this->m_clSOAPProxy->getSubListContent($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $idenreg
     * @param string     $idcolonne
     * @param string     $idContext
     * @param array      $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oDrillthrough(string $idenreg, string $idcolonne, string $idContext, array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $clParam = $this->_oGetParam(DrillThrough::class, $tabParamQuery);
        $clParam->Record = $idenreg;
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $clReponseXML = $this->m_clSOAPProxy->drillThrough($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array      $tabParamQuery
     * @param array|null $tabHeaderQuery
     * @param string     $idContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetChart(string $idContexte, array $tabParamQuery, ?array $tabHeaderQuery=null) : ActionResult
    {
        $getChart = $this->_oGetParam(GetChart::class, $tabParamQuery);
        $getChart->Width = 5000;
        $getChart->Height = 5000;
        $getChart->DPI = 92;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContexte);

        $clReponseXML = $this->m_clSOAPProxy->getChart($getChart, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array|null $tabHeaderQuery
     * @param string     $column
     * @param string     $items
     * @param string     $idContext
     * @return array
     * @throws NOUTValidationException|\Exception
     */
    public function oSetSublistOrder(string $column, string $items, string $idContext, ?array $tabHeaderQuery=null) : array
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $setSublistOrder = new SetOrderSubList();
        $setSublistOrder->items = $items;
        $setSublistOrder->column = $column;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderSubList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else {
            throw new NOUTValidationException("No valid ReturnType");
        }
    }

    /**
     * @param array|null $tabHeaderQuery
     * @param string     $items
     * @param string     $idContext
     * @return array
     * @throws NOUTValidationException|\Exception
     */
    public function oSetFullListOrder(string $items, string $idContext, ?array $tabHeaderQuery=null) : array
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery, $idContext);

        $setSublistOrder = new SetOrderList();
        $setSublistOrder->items = $items;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else {
            throw new NOUTValidationException("No valid ReturnType");
        }
    }


    /**
     * @param string $sIDContexte
     * @param Record $clRecord
     * @param int    $autovalidate
     * @param bool   $bComplete
     * @param string $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdate(string $sIDContexte, string $idihm, Record $clRecord, int $autovalidate = SOAPProxy::AUTOVALIDATE_None, bool $bComplete=false) : ActionResult
    {

        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_InArray, '$autovalidate', $autovalidate, array(SOAPProxy::AUTOVALIDATE_None, SOAPProxy::AUTOVALIDATE_Cancel, SOAPProxy::AUTOVALIDATE_Validate));
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte, $autovalidate);

        //paramètre
        $sIDForm = $clRecord->getIDTableau();
        $sIDEnreg = $clRecord->getIDEnreg();

        $clParamUpdate              = new Update();
        $clParamUpdate->Table       = $sIDForm;
        $clParamUpdate->ParamXML    = ParametersManagement::s_sStringifyParamXML([$sIDForm=>$sIDEnreg]);

        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm);
        $clParamUpdate->Complete = $bComplete ? 1 : 0;

        $clReponseXML = $this->m_clSOAPProxy->update($clParamUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        if ($autovalidate == SOAPProxy::AUTOVALIDATE_None)
        {
            //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
            $clRecortRes = $oRet->getData();
            if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
            {
                throw new \Exception("l'update n'a pas retourné le bon enregistrement");
            }

            //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
            $clRecord->updateFromRecord($clRecortRes);
            $oRet->setData($clRecord);
        }

        return $oRet;
    }


    /**
     * @param string $sIDContexte
     * @param Record $clRecord
     * @param string $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateFilter(string $sIDContexte, string $idihm, Record $clRecord) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        //paramètre
        $clParamUpdate              = new UpdateFilter();
        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->ID = $clRecord->getIDEnreg();
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm, true);

        $clReponseXML = $this->m_clSOAPProxy->updateFilter($clParamUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
        $clRecortRes = $oRet->getData();
        if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
        {
            throw new \Exception("l'update n'a pas retourné le bon enregistrement");
        }

        //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
        $clRecord->updateFromRecord($clRecortRes);
        $oRet->setData($clRecord);

        return $oRet;
    }


    /**
     * @param string $idContext
     * @param string $items
     * @param string $CallingColumn
     * @param Record $clRecord
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectItems(string $idContext, string $items, string $CallingColumn, Record $clRecord) : ActionResult
    {
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $idContext);

        $clParamSelectItems                 = new SelectItems();
        $clParamSelectItems->items          = $items;
        $clParamSelectItems->CallingColumn  = $CallingColumn;

        $clReponseXML = $this->m_clSOAPProxy->selectItems($clParamSelectItems, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        //c'est un update tout bête sans validation normalement on à le même enregistrement en entrée et en sortie
        $clRecortRes = $oRet->getData();
        if ($clRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
        {
            throw new \Exception("l'update n'a pas retourné le bon enregistrement");
        }

        //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
        $clRecord->updateFromRecord($clRecortRes);
        $oRet->setData($clRecord);

        return $oRet;
    }

    /**
     * @param string $sIDContexte
     * @param string $idButton
     * @param string $ColumnSelection
     * @param Record|null $dataRecord
     * @return ActionResult
     * @throws \Exception
     */
    public function oButtonAction(string $sIDContexte, string $idButton, string $ColumnSelection, Record $dataRecord = null) : ActionResult
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        //paramètre
        $clParam                = new ButtonAction();
        $clParam->CallingColumn = $idButton;
        $clParam->ColumnSelection = $ColumnSelection;


        $clReponseXML       = $this->m_clSOAPProxy->buttonAction($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);
        if($dataRecord !== null) {
            $clRecortRes = $oRet->getData();
            if ($dataRecord->getIDEnreg() != $clRecortRes->getIDEnreg())
            {
                throw new \Exception("l'action du bouton n'a pas retourné le bon enregistrement");
            }
            $dataRecord->updateFromRecord($clRecortRes);
            $oRet->setData($dataRecord);
        }

        return $oRet;
    }

    /**
     * Valide l'action courante du contexte
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oValidate(string $sIDContexte) : ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->validate($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Valide l'action courante du contexte
     * @param string $sIDContexte
     * @param int    $final
     * @param string $form
     * @param string $record
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateFrom(string $sIDContexte, string $form, string $record, int $final) :ActionResult
    {
        //paramètre de l'action liste
        $clCreateFrom = new CreateFrom();
        $clCreateFrom->ElemSrc = $record;
        $clCreateFrom->Table = $form;
        $clCreateFrom->TableSrc = $form;
        $clCreateFrom->Final = $final;

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->createFrom($clCreateFrom, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDContexte
     * @param string $form
     * @param string $dstRecord
     * @param string $srcRecords
     * @return ActionResult
     * @throws \Exception
     */
    public function oMerge(string $sIDContexte, string $form, string $dstRecord, string $srcRecords) :ActionResult
    {
        //paramètre de l'action liste
        $clMerge = new  Merge();
        $clMerge->ElemSrc = $srcRecords;
        $clMerge->Table = $form;
        $clMerge->ElemDest= $dstRecord;

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clReponseXML = $this->m_clSOAPProxy->merge($clMerge, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * annulation
     * @param string $sIDContexte
     * @param bool $bAll tout le contexte
     * @param bool $bByUser action utilisateur
     * @return ActionResult
     * @throws \Exception
     */
    public function oCancel(string $sIDContexte, bool $bAll = false, bool $bByUser = true) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamCancel = new Cancel();
        $clParamCancel->Context = $bAll ? 1 : 0;
        $clParamCancel->ByUser = $bByUser ? 1 : 0;

        $clReponseXML = $this->m_clSOAPProxy->cancel($clParamCancel, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param string $sIDContexte
     * @param string $ResponseValue
     * @return ActionResult
     * @throws \Exception
     */
    public function oConfirmResponse(string $sIDContexte, string $ResponseValue) :ActionResult
    {
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $oConfirmResponse = new ConfirmResponse();
        $oConfirmResponse->TypeConfirmation = $ResponseValue;

        $clReponseXML = $this->m_clSOAPProxy->ConfirmResponse($oConfirmResponse, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    // ------------------------------------------------------------------------------------
    // pour les Elements liés et les sous-listes

    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectElem(array $tabParamQuery, string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

//        public $ParamXML; 			// string
//        public $SpecialParamList; 	// SpecialParamListType
//        public $Checksum; 			// integer
//        public $DisplayMode; 		    // DisplayModeParamEnum
        $clParam = $this->_oGetParam(Search::class, $tabParamQuery);
        // Ajout des paramètres
        $clParam->Table = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->search($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateElem(array $tabParamQuery, string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParam = $this->_oGetParam(Create::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->create($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param array  $tabParamQuery
     * @param string $sIDFormulaire
     * @param string $sIDEnreg
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oDeleteElem(array $tabParamQuery, string $sIDFormulaire, string $sIDEnreg, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParam = $this->_oGetParam(Delete::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;
        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([$sIDFormulaire=>$sIDEnreg]);

        $clReponseXML = $this->m_clSOAPProxy->delete($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array          $tabParamQuery
     * @param string         $sIDContexte
     * @param \stdClass|null $updateData
     * @param string         $idenreg
     * @param string         $idformulaire
     * @param int            $autovalidate
     * @return ActionResult
     * @throws \Exception
     */
    public function oModifyElem(array $tabParamQuery, string $sIDContexte, string $idformulaire, string $idenreg, \stdClass $updateData = null, int $autovalidate = SOAPProxy::AUTOVALIDATE_None) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte, $autovalidate);

        $clParamModify = $this->_oGetParam(Modify::class, $tabParamQuery);
        $clParamModify->Table = $idformulaire;
        $clParamModify->ParamXML .= ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        if(!is_null($updateData)) {
            $clParamModify->UpdateData = ParametersManagement::s_sStringifyUpdateData($idformulaire, [$updateData->idColumn=>$updateData->val]);
        }

        $clReponseXML = $this->m_clSOAPProxy->modify($clParamModify, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array  $tabParamQuery
     * @param string $sIDContexte
     * @param string $idenreg
     * @param string $idformulaire
     * @return ActionResult
     * @throws \Exception
     */
    public function oDisplayElem(array $tabParamQuery, string $sIDContexte, string $idformulaire, string $idenreg) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamDisplay = $this->_oGetParam(Display::class, $tabParamQuery);
        $clParamDisplay->Table = $idformulaire;
        $clParamDisplay->ParamXML = ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        $clReponseXML = $this->m_clSOAPProxy->display($clParamDisplay, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDFormulaire
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectAmbiguous(string $sIDFormulaire, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDFormulaire', $sIDFormulaire, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        // Paramètres obligatoires
        $clParamSelect = new SelectForm();
        $clParamSelect->Form = $sIDFormulaire;

        $clReponseXML = $this->m_clSOAPProxy->selectForm($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param string $sIDTemplate
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectTemplate(string $sIDTemplate, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDTemplate', $sIDTemplate, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        // Paramètres obligatoires
        $clParamSelect = new SelectPrintTemplate();
        $clParamSelect->Template = $sIDTemplate;

        $clReponseXML = $this->m_clSOAPProxy->selectPrintTemplate($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $sIDChoice
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectChoice(string $sIDChoice, string $sIDContexte) :ActionResult
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDChoice', $sIDChoice, null);

        //header
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, $sIDContexte);

        $clParamSelect = new SelectChoice();
        $clParamSelect->Choice = $sIDChoice;

        $clReponseXML = $this->m_clSOAPProxy->selectChoice($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetStartAutomatism() :ActionResult
    {
        // Informations d'authentification
        /*
        $token          = $this->_oGetToken();
        $sessionToken   = $token->getSessionToken();
        $usernameToken  = $this->_oGetUsernameToken($token);
        */

        $clParamStartAutomatism = new GetStartAutomatism();

        // Paramètres : GetStartAutomatism $clWsdlType_GetStartAutomatism, $aHeaders = array()
        $clReponseXML = $this->m_clSOAPProxy->getStartAutomatism($clParamStartAutomatism, $this->_aGetTabHeader([]));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    // Fin Elements liés et les sous-listes
    // ------------------------------------------------------------------------------------

    /**
     * @param string $idContext
     * @param string $startTime
     * @param string $endTime
     * @return ActionResult
     * @throws \Exception
     */
    public function getSchedulerInfo(string $idContext, string $startTime, string $endTime) : ActionResult
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->oGetSchedulerInfo($aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param string $idContext
     * @param string $startTime
     * @param string $endTime
     * @param string $idForm
     * @param string $idEnreg
     * @param string $idColumn
     * @return ActionResult
     * @throws \Exception
     */
    public function getSchedulerCardInfo(string $idContext, string $idForm, string $idEnreg, string $idColumn, string $startTime, string $endTime) :ActionResult
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->oGetSchedulerCardInfo($idForm, $idEnreg, $idColumn, $aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idcallingcolumn
     * @param string $query
     * @return ActionResult
     * @throws \Exception
     */
    public function getSuggest(string $idcontext, string $idformulaire, string $idcallingcolumn, string $query) : ActionResult
    {
        $oSuggestData = $this->_getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($oSuggestData);

        // Modifier des données au besoin..
        //
        return $clActionResult;
    }

    /**
     * @param string $idcontext
     * @param string $idformulaire
     * @param string $idcallingcolumn
     * @param string $query
     * @return HTTPResponse
     * @throws \Exception
     */
    private function _getSuggest(string $idcontext, string $idformulaire, string $idcallingcolumn, string $query) : HTTPResponse
    {
        // Création des options
        $aTabOption = array();
        $aTabParam = array(RESTProxy::PARAM_CallingColumn => $idcallingcolumn);

        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        return $this->m_clRESTProxy->oGetSuggestFromQuery(
            $idformulaire,
            $query,
            $aTabParam,
            $aTabOption,
            $clIdentification
        );
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


    // FICHIERS
    /**
     * récupère une icône, écrit le fichier de l'icône dans le cache s'il n'existe pas déjà
     * @param string $sIDIcon
     * @param array  $aTabOptions
     * @param string $sIDColonne
     * @param string $sIDFormulaire
     * @param array  $aTabPHPManipulation
     * @return ActionResult
     * @throws \Exception
     */
    public function getImageFromLangage(string $sIDFormulaire, string $sIDColonne, string $sIDIcon, array $aTabOptions, array $aTabPHPManipulation=array()) : ActionResult
    {
        //le retour c'est le chemin de fichier enregistré dans le cache
        $oHTTPResponse = $this->_getImageFromLangage($sIDFormulaire, $sIDColonne, $sIDIcon, $aTabOptions, $aTabPHPManipulation);
        return $this->_oMakeResultFromFile($oHTTPResponse);
    }

    /**
     * récupère une image du langage
     * @param array  $aTabOptions
     * @param string $sIDColonne
     * @param string $sIDFormulaire
     * @param array  $aTabPHPManipulation
     * @param string $sIDEnreg
     * @return NOUTFileInfo
     * @throws \Exception
     */
    protected function _getImageFromLangage(string $sIDFormulaire, string $sIDColonne, string $sIDEnreg, array $aTabOptions, array $aTabPHPManipulation=array()) : NOUTFileInfo
    {
        //on veut le contenu
        $aTabOptions[RESTProxy::OPTION_WantContent] = 1;

        $clIdentification = $this->_clGetIdentificationREST('', true);


        $aTabOptionsForName = $aTabOptions;
        if (count($aTabPHPManipulation)>0)
        {
            foreach($aTabPHPManipulation as $name=>$option)
            {
                $aTabOptionsForName[$name]=$option['value'];
            }

            if (!is_null($this->m_clCache)){
                $oNOUTFileInfo = $this->m_clCache->fetchImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptionsForName);
                if (isset($oNOUTFileInfo) && ($oNOUTFileInfo !== false)){
                    return $oNOUTFileInfo; //on l'image manipuler, on la récupère
                }
            }
        }

        if (!is_null($this->m_clCache)){
            $oNOUTFileInfo = $this->m_clCache->fetchImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptions);
        }

        if (!isset($oNOUTFileInfo) || ($oNOUTFileInfo === false))
        {
            //on a pas l'image en cache avec les options en question, il faut la récupérer
            /** @var NOUTFileInfo $oFileInRecord */
            $oNOUTFileInfo  = $this->m_clRESTProxy->oGetFileInRecord(
                $sIDFormulaire,
                $sIDEnreg,
                $sIDColonne,
                array(),
                $aTabOptions,
                $clIdentification
            );

            if (!is_null($this->m_clCache))
            {
                //on sauve l'image non manipulée
                $this->m_clCache->saveImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptions, $oNOUTFileInfo);
            }
        }

        //on applique les modifications
        if (count($aTabPHPManipulation)>0)
        {
            foreach($aTabPHPManipulation as $name=>$option)
            {
                if (!call_user_func($option['callback'], $option['value'], $oNOUTFileInfo))
                {
                    $this->m_clCache->deleteImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptions);
                    $this->m_clCache->deleteImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptionsForName);
                    $oNOUTFileInfo->setNoCache(true);
                    return $oNOUTFileInfo;
                }
            }

            if (!is_null($this->m_clCache))
            {
                //on sauve l'image modifiée
                $this->m_clCache->saveImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptionsForName, $oNOUTFileInfo);
            }
        }


        return $oNOUTFileInfo;
    }

    // Langage::TABL_ModeleFichier

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

    /**
     * @param array|null $requestHeaders
     * @param array $requestParams
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetFolderList(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $clReponseXML = $this->m_clSOAPProxy->getFolderList($this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Dossier);
    }

    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @param string     $folderID
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetFolderContent(string $folderID, array $requestParams, ?array $requestHeaders=null): ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $folderContent = new GetContentFolder();
        $folderContent->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $folderContent->IDFolder = $folderID;

        $clReponseXML = $this->m_clSOAPProxy->getContentFolder($folderContent, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array      $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetMessageRequest(array $requestParams, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $requestMessage = new RequestMessage();
        $requestMessage->SpecialParamList = $requestParams[self::PARAM_SPECIALPARAMLIST];
        $requestMessage->StartDate = $requestParams[self::PARAMMESS_StartDate];
        $requestMessage->EndDate = $requestParams[self::PARAMMESS_EndDate];
        $requestMessage->Filter = $requestParams[self::PARAMMESS_Filter];

        $clReponseXML = $this->m_clSOAPProxy->getRequestMessage($requestMessage, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string $idmessage
     * @param int    $autovalidate
     * @param array  $updateData
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateMessage(string $idmessage, array $updateData, int $autovalidate = SOAPProxy::AUTOVALIDATE_None) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl([], null, $autovalidate);
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->{SOAPProxy::HEADER_OptionDialogue_ListContentAsync} = 0;

        $updateMessage = new UpdateMessage();
        $updateMessage->IDMessage=$idmessage;
        $updateMessage->UpdateData = ParametersManagement::s_sStringifyUpdateData(Langage::TABL_Messagerie_Message, $updateData);

        $clReponseXML = $this->m_clSOAPProxy->updateMessage($updateMessage, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messages
     * @param string     $column
     * @param string     $value
     * @param int        $autovalidate
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateMessages(string $messages, string $column, string $value, int $autovalidate, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders, null, $autovalidate);

        $updateMessages = new UpdateColumnMessageValueInBatch();
        $updateMessages->IDMessage = $messages;
        $updateMessages->Column = $column;
        $updateMessages->Value = $value;

        $clReponseXML =  $this->m_clSOAPProxy->updateMessages($updateMessages, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $type
     * @param string     $originalMessage
     * @param string     $templateId
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateMessage(string $type, string $originalMessage, string $templateId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $message = new CreateMessage();
        $message->CreateType = $type;
        $message->IDAnswerType = $templateId;
        if($originalMessage !== 'undefined')
            $message->IDMessage = $originalMessage;

        $clReponseXML=$this->m_clSOAPProxy->createMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetReplyTemplates() : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(null, null, SOAPProxy::AUTOVALIDATE_Cancel);

        $specialParamList = new SpecialParamListType();
        $specialParamList->First = 0;
        $specialParamList->Length = 200;
        $specialParamList->WithEndCalculation = 0;

        $execaction = new Execute();
        $execaction->ID = Langage::ACTION_RechercherReponseType;
        $execaction->SpecialParamList = $specialParamList;

        $clReponseXML = $this->m_clSOAPProxy->execute($execaction, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string     $messageID
     * @param array|null $requestParams
     * @param array|null $requestHeaders
     * @return ActionResult
     * @throws \Exception
     */
    public function oReadMessage(string $messageID, ?array $requestParams, ?array $requestHeaders=null) :ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        //$asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;

        $message = new ModifyMessage();
        $message->IDMessage = $messageID;
        $clReponseXML = $this->m_clSOAPProxy->modifyMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageID
     * @return ActionResult
     * @throws \Exception
     */
    public function oSendMessage(string $messageID, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        //$asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        //$aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;

        $message = new SendMessage();
        $message->IDMessage = $messageID;
        $clReponseXML = $this->m_clSOAPProxy->sendMessage($message, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageId
     * @param string     $attachmentId
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetAttachment(string $messageId, string $attachmentId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $getPJ = new GetPJ();
        $getPJ->IDMessage = $messageId;
        $getPJ->IDPJ = $attachmentId;

        $clReponseXML =  $this->m_clSOAPProxy->getPJ($getPJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, null, XMLResponseWS::VIRTUALRETURNTYPE_MAILSERVICERECORD_PJ);
    }

    /**
     * @param string $messageId
     * @return HTTPResponse
     * @throws \Exception
     */
    public function oPrintMessage(string $messageId) : HTTPResponse
    {
        $clIdentification = $this->_clGetIdentificationREST(null, false);
        return $this->m_clRESTProxy->oPrintMessage($messageId, $clIdentification);
    }

    /**
     * @param array|null $requestHeaders
     * @param DataPJType $PJType
     * @param string     $messageId
     * @return ActionResult
     * @throws \Exception
     */
    public function oAddAttachment(string $messageId, DataPJType $PJType, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $addPJ = new AddPJ();
        $addPJ->IDMessage = $messageId;
        $addPJ->DataPJ = $PJType;

        $clReponseXML = $this->m_clSOAPProxy->addPJ($addPJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param array|null $requestHeaders
     * @param string     $messageId
     * @param string     $attachmentId
     * @return ActionResult
     * @throws \Exception
     */
    public function oDeleteAttachment(string $messageId, string $attachmentId, ?array $requestHeaders=null) : ActionResult
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);

        $deletePJ = new DeletePJ();
        $deletePJ->IDMessage = $messageId;
        $deletePJ->IDPJ = $attachmentId;

        $clReponseXML = $this->m_clSOAPProxy->deletePj($deletePJ, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML, Langage::TABL_Messagerie_Message);
    }

    /**
     * @param string $compteID
     * @return string
     * @throws \Exception
     */
    public function sGetSignature(string $compteID) : string
    {
        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=[];

        $oRetHTTP = $this->m_clRESTProxy->oGetColInRecord(Langage::TABL_CompteEmail, $compteID, Langage::COL_COMPTEEMAIL_Signature, $aTabParam, $aTabOption, $clIdentification);

        return $oRetHTTP->content;
    }

    /**
     * @param string $compteID
     * @param string $sType
     * @param bool   $withOriginalMessage
     * @return bool
     * @throws \Exception
     */
    public function bGetSiAjouteSignature(string $compteID, string $sType, bool $withOriginalMessage) : bool
    {
        $nIDCol=(($sType==CreateMessage::CREATE_TYPE_EMPTY) || (($sType==CreateMessage::CREATE_TYPE_ANSWER_TYPE) && !$withOriginalMessage))
            ? Langage::COL_COMPTEEMAIL_SignatureNouveau
            : Langage::COL_COMPTEEMAIL_SignatureRepondre;

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=['displayvalue' => 0];

        $oRetHTTP = $this->m_clRESTProxy->oGetColInRecord(Langage::TABL_CompteEmail, $compteID, $nIDCol, $aTabParam, $aTabOption, $clIdentification);
        $sRes= $oRetHTTP->content;

        return ($sRes==="Oui") || ($sRes==="Vrai") || intval($sRes) <> 0;
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