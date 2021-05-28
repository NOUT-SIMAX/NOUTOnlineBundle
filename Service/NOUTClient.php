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
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserNumberOfChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\SelectorList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Messaging\FolderList;
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
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserXmlXsd;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserScheduler;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserXSDSchema;
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
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\FilterType;
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
     * @param $sMessage
     * @param int $nCode
     * @throws \Exception
     */
    protected function _ThrowError($sMessage, $nCode = 0)
    {
        throw new \Exception($sMessage, $nCode);
    }

    /**
     * récupère le numéro de version
     * @return NOUTOnlineVersion
     */
    public function clGetVersion()
    {
        return $this->m_clRESTProxy->clGetVersion();
    }

    /**
     * teste le client pour savoir s'il correspond à la version minimale
     * @return bool
     */
    public function isVersionMin()
    {
        return $this->clGetVersion()->isVersionSup($this->m_sVersionMin, true);
    }

    /**
     * @return NOUTCacheProvider
     */
    public function getCacheSession()
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
    public function getTimeZone()
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
    protected function _clGetOptionDialogue()
    {
        return $this->m_clOptionDialogue;
    }

    /**
     * @param NOUTToken $oToken
     * @return UsernameToken
     */
    protected function _oGetUsernameToken(NOUTToken $oToken)
    {
        return $oToken->getUsernameToken();
    }

    /**
     * @param $sIDContexteAction
     * @param $bAPIUser
     * @return Identification
     */
    protected function _clGetIdentificationREST($sIDContexteAction, $bAPIUser)
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
     * @param array|null $aHeaderSup
     * @return array
     */
    protected function _aGetTabHeader(array $aHeaderSup = null)
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
        if (!empty($aHeaderSup))
        {
            $aTabHeader = array_merge($aTabHeader, $aHeaderSup);
        }

        return $aTabHeader;
    }

    /**
     * @param string $sIDform identifiant du formulaire
     * @param ConditionFileNPI $clFileNPI condition pour la requête
     * @param array $TabColonneAff tableau des colonnes a afficher
     * @param array $TabHeaderSuppl tableau des headers
     * @throws \Exception
     * @return XMLResponseWS
     */
    protected function _oRequest(string $sIDform, ConditionFileNPI $clFileNPI, array $TabColonneAff, array $TabHeaderSuppl)
    {
        $clParamRequest = new Request();
        $clParamRequest->Table = $sIDform;
        $clParamRequest->CondList = $clFileNPI->sToSoap();
        $clParamRequest->ColList = new ColListType($TabColonneAff);

        return $this->m_clSOAPProxy->request($clParamRequest, $this->_aGetTabHeader($TabHeaderSuppl));
    }

    /**
     * @param $table
     * @param CondListType $condList
     * @param array $colList
     * @param array $tabHeaderSuppl
     * @return XMLResponseWS
     */
    protected function _oNewRequest($table, CondListType $condList, array $colList, array $tabHeaderSuppl)
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
     * @param $idCol
     * @throws \Exception
     * @return XMLResponseWS
     */
    protected function _oGetTabIcon($idCol)
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
     * @throws \Exception
     * @return XMLResponseWS
     */
    protected function _oGetTabMenu_OptionMenu()
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
     * @throws \Exception
     * @return XMLResponseWS
     */
    protected function _oGetTabMenu_Menu()
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
     * @return string
     * @param $idContext
     */
    public function getHelp($idContext)
    {
        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        return $this->m_clRESTProxy->sGetHelp($clIdentification);
    }

    /**
     * récupère les infos d'ihm lié au menu (menu, toolbar, et icône centraux)
     * c'est sauvé dans le cache de session à cause de la Formule Visible des menu et option de menu
     *  comme on peut avoir n'importe quoi dans la formule, cela ne peut pas être lié au paramétrage
     *
     * @param $method
     * @param $prefix
     * @return array|mixed|null
     */
    protected function __oGetIhmMenuPart($method, $prefix)
    {
        $sUsername = $this->_oGetToken()->getUsername();
        $aTabMenu = $this->fetchFromCache(NOUTClientCache::CACHE_Session, "info_{$prefix}_{$sUsername}");
        if (isset($aTabMenu) && ($aTabMenu !== false)){
            return $aTabMenu; //on a déjà les infos du menu
        }

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aInfo = array();
        //on a pas les infos, il faut les calculer
        $json = json_decode($this->m_clRESTProxy->$method($clIdentification), false, 512, JSON_BIGINT_AS_STRING);
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
            $this->_saveInCache(NOUTClientCache::CACHE_Session, "info_{$prefix}_{$sUsername}", $aInfo);
        }
        return $aInfo;
    }

    /**
     * récupère les infos du menu
     * @param \stdClass $objSrc
     * @return ItemMenu
     */
    protected function __oGetItemMenu(\stdClass $objSrc)
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
     * @param $member_name
     * @param $method_name
     * @param $prefix
     * @throws \Exception
     * @return ActionResult
     */
    protected function _oGetIhmMenuPart($member_name, $method_name, $prefix)
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
     * @throws \Exception
     * @return ActionResult
     */
    public function getTabMenu()
    {
        return $this->_oGetIhmMenuPart('aMenu', 'sGetMenu', 'menu');
    }


    /**
     * retourne un tableau d'option de menu
     * @throws \Exception
     * @return ActionResult
     */
    public function getCentralIcon()
    {
        return $this->_oGetIhmMenuPart('aBigIcon', 'sGetCentralIcon', 'home');
    }

    /**
     * retourne un tableau d'option de menu
     * @throws \Exception
     * @return ActionResult
     */
    public function getToolbar()
    {
        return $this->_oGetIhmMenuPart('aToolbar', 'sGetToolbar', 'toolbar');
    }

    /**
     * initialise la structure de paramètre a partir du tableau des paramètres de la requête HTTP
     * @param $classname
     * @param $aTabParamRequest
     * @return mixed
     */
    protected function _oGetParam($classname, $aTabParamRequest)
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
     * @param $aTabHeaderQuery
     * @return array
     */
    protected function _aGetHeaderSuppl($aTabHeaderQuery)
    {
        $aTabHeaderSuppl = array();

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

        return $aTabHeaderSuppl;
    }


    /**
     * Execute une action via son id
     * @param array $tabParamQuery
     * @param array $tabHeaderQuery
     * @param       $sIDAction
     * @param       $final
     * @throws \Exception
     * @return ActionResult
     */
    public function oExecIDAction($sIDAction, array $tabParamQuery, array $tabHeaderQuery = array(), $final = 0)
    {
        // Les paramètres du header sont passés par array

        //--------------------------------------------------------------------------------------------
        // Paramètres
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->ID = (string)$sIDAction;             // identifiant de l'action (String)
        $clParam->Final = $final;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        //--------------------------------------------------------------------------------------------
        // L'action
        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param array $tabParamQuery
     * @param array $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecute(array $tabParamQuery, array $tabHeaderQuery) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * Execute une action via sa phrase
     * @param array  $tabParamQuery
     * @param        $sPhrase
     * @param string $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oExecSentence(array $tabParamQuery, $sPhrase, $sIDContexte = '')
    {
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
        $clParam = $this->_oGetParam(Execute::class, $tabParamQuery);
        $clParam->Sentence = (string)$sPhrase;
        //--------------------------------------------------------------------------------------------

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        return $this->_oExecute($clParam, $aTabHeaderSuppl);
    }

    /**
     * @param string $sLoginExtranet
     * @param string $sPassword
     * @param string $sTypeEncodage
     * @param        $codeLangue
     * @param string $sLoginSIMAX
     * @param string $sPassworSIMAX
     * @param string $sFormulaireExtranet
     * @return ActionResult
     * @throws \Exception
     */
    public function oConnexionExtranet(string $sLoginExtranet, string $sPassword, string $sTypeEncodage, $codeLangue, string $sLoginSIMAX, string $sPassworSIMAX, string $sFormulaireExtranet) : ActionResult
    {
        $clParam = new Execute();
        $clParam->ID = Langage::ACTION_ConnexionExtranet;

        //il faut encoder le mot de passe simax
        $sSecretSIMAX = ($sPassworSIMAX == '') ? '00000000000000000000000000000000' : bin2hex(md5(  $sPassworSIMAX,true ));
        $sEncodedSIMAX = bin2hex(sha1($sSecretSIMAX, true));

        switch ($sTypeEncodage)
        {
            case Langage::PASSWORD_ENCODAGE_plaintext:
            case Langage::PASSWORD_ENCODAGE_sha1:
                $sEncodedExtranet = bin2hex(hash('sha1', $sPassword, true));
                break;
            case Langage::PASSWORD_ENCODAGE_sha256:
                $sEncodedExtranet = bin2hex(hash('sha256', $sPassword, true));
                break;
            case Langage::PASSWORD_ENCODAGE_md5:
                $sEncodedExtranet = bin2hex(hash('md5', $sPassword, true));
                break;
        }

        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([
            Langage::PA_ConnexionExtranet_Extranet_Pseudo => $sLoginExtranet,
            Langage::PA_ConnexionExtranet_Extranet_Mdp => $sEncodedExtranet,
            Langage::PA_ConnexionExtranet_Intranet_Pseudo => $sLoginSIMAX,
            Langage::PA_ConnexionExtranet_Intranet_Mdp => $sEncodedSIMAX,
            Langage::PA_ConnexionExtranet_Formulaire => $sFormulaireExtranet,
            Langage::PA_ConnexionExtranet_Langue => $codeLangue,
        ]);

        //on execute l'action
        try{
            $oRet = $this->_oExecute($clParam, []);
        }
        catch (\Exception $e){
            throw $e; //on fait suivre l'exception
        }
        //ici il faut invalider le cache
        //$this->m_clCache
        return $oRet;
    }

    /**
     * @param array  $tabParamQuery
     * @param        $sIDTableau
     * @param string $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oExecList(array $tabParamQuery, $sIDTableau, $sIDContexte = '')
    {
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(ListParams::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        $clReponseXML = $this->m_clSOAPProxy->listAction($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $tableID
     * @param string $contextID
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecListRequest($tableID, $contextID = '')
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Requete, Langage::COL_REQUETE_IDTableau, []);
    }

    /**
     * @param $tableID
     * @param $contextID
     * @param $requestTableId
     * @param $requestColId
     * @param $colList
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oExecRequestOnIDTableau($tableID, $contextID, $requestTableId, $requestColId, $colList)
    {
        $condition = new Condition(
            new CondColumn($requestColId),
            new CondType(CondType::COND_EQUAL),
            new CondValue($tableID));
        $condList = CondListTypeFactory::create($condition);

        $aTabHeaderSuppl = array();
        if(!empty($contextID))
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $contextID;

        $clReponseXML = $this->_oNewRequest(
            $requestTableId,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oExecListCalculation(string $contextID)
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(array());
        $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $contextID;

        $clReponseXML = $this->m_clSOAPProxy->getEndListCalculation($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetDefaultExportAction(string $contextID)
    {
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

        $aTabHeaderSuppl = array();
        if(!empty($contextID))
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $contextID;

        $clReponseXML = $this->_oNewRequest(Langage::TABL_Action,
            $condList,
            $aTabColonne,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetExportsList(string $tableID, string $contextID)
    {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Export, Langage::COL_EXPORT_IDTableau, [Langage::COL_EXPORT_Libelle]);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetImportsList(string $tableID, string $contextID) {
        return $this->_oExecRequestOnIDTableau($tableID, $contextID, Langage::TABL_Import, Langage::COL_IMPORT_Formulaire, [Langage::COL_IMPORT_Libelle]);
    }

    /**
     * @param $ctxtId
     * @param $tableId
     * @param $actionId
     * @param $exportId
     * @param $format
     * @param $module
     * @param $colType
     * @param $items
     * @return ActionResult
     * @throws \Exception
     */
    public function oExport($ctxtId, $tableId, $actionId, $exportId, $format, $module, $colType, $items) {
        $export = new Export();
        $export->Table = $tableId;
        $export->ID = $actionId;
        $export->Export = $exportId;
        $export->Format = $format;
        $export->Module = $module;
        $export->ColType = $colType;
        $export->items = $items;

        $clReponseXML = $this->m_clSOAPProxy->export($export, $this->_aGetTabHeader(array(SOAPProxy::HEADER_ActionContext => $ctxtId)));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $tableId
     * @param $actionId
     * @param $importId
     * @param $file
     * @return ActionResult
     * @throws \Exception
     */
    public function oImport($tableId, $actionId, $importId, UploadedFile $file = null) {
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

        $clReponseXML = $this->m_clSOAPProxy->import($import, $this->_aGetTabHeader(array()));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $tableID
     * @param $contextID
     * @param $eTypeAction
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oRequestImportExportActions($tableID, $contextID, $eTypeAction)
    {
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

        $aTabHeaderSuppl = array();
        if(!empty($contextID))
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $contextID;

        $clReponseXML = $this->_oNewRequest(
            Langage::TABL_Action,
            $condList,
            $colList,
            $aTabHeaderSuppl);

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetExportsActions(string $tableID, string $contextID)
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Exporter);
    }

    /**
     * @param string $tableID
     * @param string $contextID
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetImportsActions(string $tableID, string $contextID)
    {
        return $this->_oRequestImportExportActions($tableID, $contextID, Langage::eTYPEACTION_Importer);
    }

    /**
     * Affichage d'une liste via l'action recherche
     * @param array $tabParamQuery
     * @param $sIDTableau
     * @param string $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oExecSearch(array $tabParamQuery, $sIDTableau, $sIDContexte = '')
    {
        //paramètre de l'action liste
        $clParam = $this->_oGetParam(Search::class, $tabParamQuery);
        $clParam->Table = $sIDTableau;

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        $clReponseXML = $this->m_clSOAPProxy->search($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param Execute $clParamExecute
     * @param array $aTabHeaderSuppl
     * @throws \Exception
     * @return ActionResult
     */
    protected function _oExecute(Execute $clParamExecute, array $aTabHeaderSuppl)
    {
        $clReponseXML = $this->m_clSOAPProxy->execute($clParamExecute, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Execute une action via son id
     * @param array $tabParamQuery
     * @param array $tabHeaderQuery
     * @param string $idcolonne
     * @param Record $clRecord
     * @return ActionResult
     *@throws \Exception
     */
    public function oGetSublistContent(Record $clRecord, string $idcolonne, array $tabParamQuery, array $tabHeaderQuery = array())
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$idColumn', $idcolonne, null);

        //paramètre de l'action liste
        $clParam = $this->_oGetParam(GetSubListContent::class, $tabParamQuery);
        $clParam->Record = $clRecord->getIDEnreg();
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        $clReponseXML = $this->m_clSOAPProxy->getSubListContent($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param string $idenreg
     * @param int    $idcolonne
     * @param array  $tabParamQuery
     * @param array  $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oDrillthrough(string $idenreg, int $idcolonne, array $tabParamQuery, array $tabHeaderQuery = array())
    {
        $clParam = $this->_oGetParam(DrillThrough::class, $tabParamQuery);
        $clParam->Record = $idenreg;
        $clParam->Column = $idcolonne;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        $clReponseXML = $this->m_clSOAPProxy->drillThrough($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array $tabParamQuery
     * @param array $tabHeaderQuery
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetChart(array $tabParamQuery, array $tabHeaderQuery = array())
    {
        $getChart = $this->_oGetParam(GetChart::class, $tabParamQuery);
        $getChart->Width = 5000;
        $getChart->Height = 5000;
        $getChart->DPI = 92;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        $clReponseXML = $this->m_clSOAPProxy->getChart($getChart, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param array $tabHeaderQuery
     * @param $column
     * @param $items
     * @return array
     * @throws NOUTValidationException
     */
    public function oSetSublistOrder($column, $items, array $tabHeaderQuery = array()) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        $setSublistOrder = new SetOrderSubList();
        $setSublistOrder->items = $items;
        $setSublistOrder->column = $column;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderSubList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else throw new NOUTValidationException("No valid ReturnType");
    }

    /**
     * @param array $tabHeaderQuery
     * @param $items
     * @return array
     * @throws NOUTValidationException
     */
    public function oSetFullListOrder($items, array $tabHeaderQuery = array()) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($tabHeaderQuery);

        $setSublistOrder = new SetOrderList();
        $setSublistOrder->items = $items;

        $clXMLResponse = $this->m_clSOAPProxy->setOrderList($setSublistOrder, $this->_aGetTabHeader($aTabHeaderSuppl));

        if($clXMLResponse->sGetReturnType() === XMLResponseWS::RETURNTYPE_VALUE) {
            return explode('|', trim($clXMLResponse->getValue(), '|'));
        }
        else throw new NOUTValidationException("No valid ReturnType");
    }

    private function __startStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->start($eventName);
        }
    }

    private function __stopStopwatch($eventName){
        if (isset($this->__stopwatch)){
            $this->__stopwatch->stop($eventName);
        }
    }

    private function _getStopWatchEventName($function, $plus)
    {
        return get_class($this).'::'.$function.(empty($plus) ? '' : '::'.$plus);
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @param $ReturnTypeForce
     * @return ActionResult
     * @throws \Exception
     */
    protected function _oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, $ReturnTypeForce = null)
    {
        $clActionResult = new ActionResult($clReponseXML);
        if (!empty($ReturnTypeForce))
        {
            $clActionResult->ReturnType = $ReturnTypeForce; //on force le return type
        }


        $this->__startStopwatch($stopWatchEvent = $this->_getStopWatchEventName(__FUNCTION__, $clActionResult->ReturnType));
        switch ($clActionResult->ReturnType)
        {
            case XMLResponseWS::RETURNTYPE_EMPTY:
                break; //on ne fait rien de plus

            case XMLResponseWS::RETURNTYPE_VALUE:

            case XMLResponseWS::RETURNTYPE_XSD:
            case XMLResponseWS::RETURNTYPE_IDENTIFICATION:
            case XMLResponseWS::RETURNTYPE_PLANNING:
            case XMLResponseWS::RETURNTYPE_LISTCALCULATION:
            case XMLResponseWS::RETURNTYPE_EXCEPTION:


            case XMLResponseWS::RETURNTYPE_MAILSERVICERECORD:
            case XMLResponseWS::RETURNTYPE_MAILSERVICESTATUS:
            case XMLResponseWS::RETURNTYPE_WITHAUTOMATICRESPONSE:
            {
                $this->__stopStopwatch($stopWatchEvent);
                throw new \Exception("Type de retour $clActionResult->ReturnType non géré", 1);
            }

            case XMLResponseWS::RETURNTYPE_MAILSERVICELIST:
            {
                $idColumn = 'id_' . Langage::COL_MESSAGERIE_IDDossier;
                $idName = 'id_' . Langage::COL_MESSAGERIE_Libelle;
                $idParent = 'id_' . Langage::COL_MESSAGERIE_IDDossierPere;
                $list = new FolderList();

                foreach($clReponseXML->getNodeXML()->children() as $type => $child) {
                    $id = (string) $child->$idColumn;
                    $name = (string) $child->$idName;
                    $parentID = (string) $child->$idParent;
                    $list->add($id, $name, $parentID);
                }
                //var_dump($list);
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

            case XMLResponseWS::RETURNTYPE_VALIDATERECORD:
            case XMLResponseWS::RETURNTYPE_RECORD:
            case XMLResponseWS::RETURNTYPE_VALIDATEACTION:
            {
                // Instance d'un parser
                $clResponseParser = new ReponseWSParser();
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                /** @var ParserXmlXsd $clParser */
                $clActionResult->setData($clParser->getRecord($clReponseXML));
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
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                // dump($clParser);
                // clParser est bien du type ParserList mais n'a pas encore les données

                // getList renvoi un RecordList
                $list = $clParser->getList();
                // dump($list);

                $clActionResult
                    ->setData($list)
                    ->setValidateError($clReponseXML->getValidateError())
                    ->setCount($clCount);

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
     * @param $sIDContexte
     * @param Record $clRecord
     * @param int $autovalidate
     * @param boolean $bComplete
     * @param $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdate($sIDContexte, $idihm, Record $clRecord, $autovalidate = SOAPProxy::AUTOVALIDATE_None, $bComplete=false)
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_InArray, '$autovalidate', $autovalidate, array(SOAPProxy::AUTOVALIDATE_None, SOAPProxy::AUTOVALIDATE_Cancel, SOAPProxy::AUTOVALIDATE_Validate));
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $sIDForm = $clRecord->getIDTableau();
        $sIDEnreg = $clRecord->getIDEnreg();

        $clParamUpdate              = new Update();
        $clParamUpdate->Table       = $sIDForm;
        $clParamUpdate->ParamXML    = ParametersManagement::s_sStringifyParamXML([$sIDForm=>$sIDEnreg]);

        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm);
        $clParamUpdate->Complete = $bComplete ? 1 : 0;

        //header
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte, SOAPProxy::HEADER_AutoValidate => $autovalidate);
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
     * @param $sIDContexte
     * @param Record $clRecord
     * @param $idihm
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdateFilter($sIDContexte, $idihm, Record $clRecord)
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);


        $clParamUpdate              = new UpdateFilter();
        //m_clRecordSerializer->getRecordUpdateData fait la gestion des fichiers
        $clParamUpdate->ID = $clRecord->getIDEnreg();
        $clParamUpdate->UpdateData = $this->m_clRecordSerializer->getRecordUpdateData($clRecord, $sIDContexte, $idihm, true);

        //header
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte);
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
    public function oSelectItems(string $idContext, string $items, string $CallingColumn, Record $clRecord)
    {
        $clParamSelectItems                 = new SelectItems();
        $clParamSelectItems->items          = $items;
        $clParamSelectItems->CallingColumn  = $CallingColumn;

        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $idContext);

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
     * @param $ColumnSelection
     * @param null $dataRecord
     * @return ActionResult
     * @throws \Exception
     */
    public function oButtonAction(string $sIDContexte, string $idButton, $ColumnSelection, $dataRecord = null)
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $clParam                = new ButtonAction();
        $clParam->CallingColumn = $idButton;
        $clParam->ColumnSelection = $ColumnSelection;

        //header
        $aTabHeaderSuppl    = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte);
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
     * @param $sIDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oValidate($sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte);
        $clReponseXML = $this->m_clSOAPProxy->validate($this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * Valide l'action courante du contexte
     * @param $sIDContexte
     * @param $final
     * @param $form
     * @param $record
     * @return ActionResult
     * @throws \Exception
     */
    public function oCreateFrom($sIDContexte, $form, $record, $final)
    {
        //paramètre de l'action liste
        $clCreateFrom = new CreateFrom();
        $clCreateFrom->ElemSrc = $record;
        $clCreateFrom->Table = $form;
        $clCreateFrom->TableSrc = $form;
        $clCreateFrom->Final = $final;

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        $clReponseXML = $this->m_clSOAPProxy->createFrom($clCreateFrom, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $sIDContexte
     * @param $form
     * @param $dstRecord
     * @param $srcRecords
     * @return ActionResult
     * @throws \Exception
     */
    public function oMerge($sIDContexte, $form, $dstRecord, $srcRecords) {
        //paramètre de l'action liste
        $clMerge = new  Merge();
        $clMerge->ElemSrc = $srcRecords;
        $clMerge->Table = $form;
        $clMerge->ElemDest= $dstRecord;

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        $clReponseXML = $this->m_clSOAPProxy->merge($clMerge, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * annulation
     * @param $sIDContexte
     * @param bool $bAll tout le contexte
     * @param bool $bByUser action utilisateur
     * @return ActionResult
     * @throws \Exception
     */
    public function oCancel($sIDContexte, $bAll = false, $bByUser = true)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte);

        $clParamCancel = new Cancel();
        $clParamCancel->Context = $bAll ? 1 : 0;
        $clParamCancel->ByUser = $bByUser ? 1 : 0;

        $clReponseXML = $this->m_clSOAPProxy->cancel($clParamCancel, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param $sIDContexte
     * @param $ResponseValue
     * @return ActionResult
     * @throws \Exception
     */
    public function oConfirmResponse($sIDContexte, $ResponseValue)
    {
        //header
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte);

        $oConfirmResponse = new ConfirmResponse();
        $oConfirmResponse->TypeConfirmation = $ResponseValue;

        $clReponseXML = $this->m_clSOAPProxy->ConfirmResponse($oConfirmResponse, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    // ------------------------------------------------------------------------------------
    // pour les Elements liés et les sous-listes

    /**
     * @param $tabParamQuery
     * @param $sIDFormulaire
     * @param $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oSelectElem(array $tabParamQuery, $sIDFormulaire, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext => $sIDContexte);


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
     * @param $tabParamQuery
     * @param $sIDFormulaire
     * @param $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oCreateElem(array $tabParamQuery, $sIDFormulaire, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        $clParam = $this->_oGetParam(Create::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->create($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param $tabParamQuery
     * @param $sIDFormulaire
     * @param $sIDEnreg
     * @param $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oDeleteElem(array $tabParamQuery, $sIDFormulaire, $sIDEnreg, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        $clParam = $this->_oGetParam(Delete::class, $tabParamQuery);
        $clParam->Table = $sIDFormulaire;
        $clParam->ParamXML = ParametersManagement::s_sStringifyParamXML([$sIDFormulaire=>$sIDEnreg]);

        $clReponseXML = $this->m_clSOAPProxy->delete($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $tabParamQuery
     * @param $sIDContexte
     * @param \stdClass|null $updateData
     * @param $idenreg
     * @param $idformulaire
     * @return ActionResult
     * @throws \Exception
     */
    public function oModifyElem(array $tabParamQuery, $sIDContexte, $idformulaire, $idenreg, $updateData = null)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $clParamModify = $this->_oGetParam(Modify::class, $tabParamQuery);
        $clParamModify->Table = $idformulaire;
        $clParamModify->ParamXML .= ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        if(!is_null($updateData)) {
            $aTabHeaderSuppl = array(
                SOAPProxy::HEADER_ActionContext => $sIDContexte,
                SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Validate
            );
            $clParamModify->UpdateData = ParametersManagement::s_sStringifyUpdateData($idformulaire, [$updateData->idColumn=>$updateData->val]);
        }
        else {
            $aTabHeaderSuppl = array(
                SOAPProxy::HEADER_ActionContext => $sIDContexte
            );
        }

        $clReponseXML = $this->m_clSOAPProxy->modify($clParamModify, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $tabParamQuery
     * @param $sIDContexte
     * @param $idenreg
     * @param $idformulaire
     * @throws \Exception
     * @return ActionResult
     */
    public function oDisplayElem(array $tabParamQuery, $sIDContexte, $idformulaire, $idenreg)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        $clParamDisplay = $this->_oGetParam(Display::class, $tabParamQuery);
        $clParamDisplay->Table = $idformulaire;
        $clParamDisplay->ParamXML = ParametersManagement::s_sStringifyParamXML([$idformulaire=>$idenreg]);

        $clReponseXML = $this->m_clSOAPProxy->display($clParamDisplay, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oSelectAmbiguous($sIDFormulaire, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDFormulaire', $sIDFormulaire, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        // Paramètres obligatoires
        $clParamSelect = new SelectForm();
        $clParamSelect->Form = $sIDFormulaire;

        $clReponseXML = $this->m_clSOAPProxy->selectForm($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    /**
     * @param $sIDTemplate
     * @param $sIDContexte
     * @throws \Exception
     * @return ActionResult
     */
    public function oSelectTemplate($sIDTemplate, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDTemplate', $sIDTemplate, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        // Paramètres obligatoires
        $clParamSelect = new SelectPrintTemplate();
        $clParamSelect->Template = $sIDTemplate;

        $clReponseXML = $this->m_clSOAPProxy->selectPrintTemplate($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $sIDChoice
     * @param $sIDDContexte
     * @return ActionResult
     * @throws \Exception
     */
    public function oSelectChoice($sIDChoice, $sIDDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDChoice', $sIDChoice, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDDContexte
        );

        $clParamSelect = new SelectChoice();
        $clParamSelect->Choice = $sIDChoice;

        $clReponseXML = $this->m_clSOAPProxy->selectChoice($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @throws \Exception
     * @return ActionResult
     */
    public function oGetStartAutomatism()
    {
        // Informations d'authentification
        /*
        $token          = $this->_oGetToken();
        $sessionToken   = $token->getSessionToken();
        $usernameToken  = $this->_oGetUsernameToken($token);
        */

        // Infos déjà récupérées par aGetTabHeader
        $aTabHeaderSuppl = array(
//            SOAPProxy::HEADER_SessionToken  => $sessionToken,
//            SOAPProxy::HEADER_UsernameToken => $usernameToken,
        );

        $clParamStartAutomatism = new GetStartAutomatism();

        // Paramètres : GetStartAutomatism $clWsdlType_GetStartAutomatism, $aHeaders = array()
        $clReponseXML = $this->m_clSOAPProxy->getStartAutomatism($clParamStartAutomatism, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }


    // Fin Elements liés et les sous-listes
    // ------------------------------------------------------------------------------------

    /**
     * @param $idContext
     * @param $startTime
     * @param $endTime
     * @throws \Exception
     * @return ActionResult
     */
    public function getSchedulerInfo($idContext, $startTime, $endTime)
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->sGetSchedulerInfo($aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param $idContext
     * @param $startTime
     * @param $endTime
     * @param $idForm
     * @param $idEnreg
     * @param $idColumn
     * @throws \Exception
     * @return ActionResult
     */
    public function getSchedulerCardInfo($idContext, $idForm, $idEnreg, $idColumn, $startTime, $endTime)
    {
        $aTabParam = array(
            RESTProxy::PARAM_StartTime  => $startTime,
            RESTProxy::PARAM_EndTime    => $endTime,
        );

        $clIdentification = $this->_clGetIdentificationREST($idContext, false);

        $sRet = $this->m_clRESTProxy->sGetSchedulerCardInfo($idForm, $idEnreg, $idColumn, $aTabParam, $clIdentification);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sRet);

        return $clActionResult;
    }

    /**
     * @param $idcontext
     * @param $idformulaire
     * @param $idcallingcolumn
     * @param $query
     * @throws \Exception
     * @return ActionResult
     */
    public function getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query)
    {
        $oSuggestData = $this->_getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($oSuggestData);

        // Modifier des données au besoin..
        //
        return $clActionResult;
    }

    /**
     * @param $idcontext
     * @param $idformulaire
     * @param $idcallingcolumn
     * @param $query
     * @throws \Exception
     * @return HTTPResponse
     */
    private function _getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query)
    {
        // Création des options
        $aTabOption = array();
        $aTabParam = array(RESTProxy::PARAM_CallingColumn => $idcallingcolumn);

        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        return $this->m_clRESTProxy->sGetSuggestFromQuery(
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
     */
    protected function _oMakeResultFromFile(NOUTFileInfo $oRet)
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
     * @param $sIDIcon
     * @param $aTabOptions
     * @param $sIDColonne
     * @param $sIDFormulaire
     * @param array $aTabPHPManipulation
     * @throws \Exception
     * @return ActionResult
     */
    public function getImageFromLangage($sIDFormulaire, $sIDColonne, $sIDIcon, $aTabOptions, $aTabPHPManipulation=array())
    {
        //le retour c'est le chemin de fichier enregistré dans le cache
        $oHTTPResponse = $this->_getImageFromLangage($sIDFormulaire, $sIDColonne, $sIDIcon, $aTabOptions, $aTabPHPManipulation);
        return $this->_oMakeResultFromFile($oHTTPResponse);
    }

    /**
     * récupère une image du langage
     * @param array $aTabOptions
     * @param $sIDColonne
     * @param $sIDFormulaire
     * @param array $aTabPHPManipulation
     * @param $sIDEnreg
     * @throws \Exception
     * @return NOUTFileInfo
     */
    protected function _getImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, array $aTabOptions, array $aTabPHPManipulation=array())
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
     * @param $idcontexte
     * @param $idihm
     * @param $idForm
     * @param $idColumn
     * @param $idRecord
     * @param array $aTabOptions
     * @throws \Exception
     * @return ActionResult
     */
    public function getFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, array $aTabOptions)
    {
        $oHTTPResponse = $this->_getFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, $aTabOptions);
        return $this->_oMakeResultFromFile($oHTTPResponse);
    }


    /**
     * récupère un fichier pour téléchargement
     * @param $idcontexte
     * @param $idihm
     * @param $idForm
     * @param $idColumn
     * @param $idRecord
     * @param array $aTabOptions
     * @throws \Exception
     * @return false|NOUTFileInfo
     */
    private function _getFile($idcontexte, $idihm, $idForm, $idColumn, $idRecord, array $aTabOptions)
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
     * @param              $idcontexte
     * @param              $idihm
     * @param              $idcolonne
     * @return ActionResult
     */
    public function saveUploadFileInCache(UploadedFile $file, $idcontexte, $idihm, $idcolonne)
    {
        $data = new NOUTFileInfo();
        $data->initFromUploadedFile($file);
        return $this->saveNOUTFileInCache($data, $idcontexte, $idihm, $idcolonne);
    }

    /**
     * @param              $idcontexte
     * @param              $idihm
     * @param              $idcolonne
     * @param $dataBase64
     * @param $mimetype
     * @return ActionResult
     */
    public function saveBase64DataInCache($dataBase64, $mimetype, $idcontexte, $idihm, $idcolonne)
    {
//        $temp_file = tempnam(sys_get_temp_dir(), 'drawing');
//        file_put_contents($temp_file, base64_decode($dataBase64));

        $data = new NOUTFileInfo();
        $data->initImgFromUploadedBase64Data($dataBase64, $mimetype, $idcolonne);
        return $this->saveNOUTFileInCache($data, $idcontexte, $idihm, $idcolonne);
    }


    /**
     * @param NOUTFileInfo $file
     * @param              $idcontexte
     * @param              $idihm
     * @param              $idcolonne
     * @return ActionResult
     */
    public function saveNOUTFileInCache(NOUTFileInfo $file, $idcontexte, $idihm, $idcolonne)
    {
        $name = $this->m_clCache->saveFile($idcontexte, $idihm, '', $idcolonne, '', array(), $file);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($name);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);

        return $clActionResult;
    }


    /**
     * @param $idcontexte
     * @param $idihm
     * @param $name
     * @return ActionResult
     */
    public function getFileInCache($idcontexte, $idihm, $name)
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
     * @param $requestParams
     * @param $requestHeaders
     * @throws \Exception
     * @return mixed
     */
    public function oGetFolderList(array $requestHeaders, $requestParams)
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $clReponseXML = $this->m_clSOAPProxy->getFolderList($aTabHeaderSuppl, $requestParams);
        return json_encode($clReponseXML->getNodeXML()->children(), JSON_UNESCAPED_UNICODE);
        //return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @param $requestParams
     * @param $requestHeaders
     * @param $folderID
     * @throws \Exception
     * @return \stdClass
     */
    public function oGetFolderContent(array $requestHeaders, $requestParams, $folderID)
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $folderContent = new GetContentFolder();
        $folderContent->SpecialParamList = $requestParams;
        $folderContent->IDFolder = $folderID;
        $clReponseXML = $this->m_clSOAPProxy->getContentFolder($folderContent, $aTabHeaderSuppl);
        $res = new \stdClass();
        $res->data = $clReponseXML->getNodeXML()->children();
        $res->totalCount = $clReponseXML->clGetFolderCount()->m_nNbReceived;
        $res->unreadCount = $clReponseXML->clGetFolderCount()->m_nNbUnread;
        return $res;
        //return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    public function oGetMessageRequest(array $requestHeaders, $requestParams, $filters, $startdate, $endDate) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $requestMessage = new RequestMessage();
        $requestMessage->SpecialParamList = $requestParams;
        $requestMessage->StartDate = $startdate;
        $requestMessage->EndDate = $endDate;
        $requestMessage->Filter = new FilterType();
        $requestMessage->Filter->Way = $filters->way;
        $requestMessage->Filter->State = $filters->state;
        $requestMessage->Filter->Inner = $filters->inner;
        $requestMessage->Filter->Email = $filters->email;
        $requestMessage->Filter->Spam = $filters->spam;
        $requestMessage->Filter->Max = $filters->max;
        $requestMessage->Filter->From = $filters->from;
        $requestMessage->Filter->Containing = $filters->containing;
        $clReponseXML = $this->m_clSOAPProxy->getRequestMesage($requestMessage, $aTabHeaderSuppl);
        $res = new \stdClass();
        $res->data = $clReponseXML->getNodeXML()->children();
        $res->totalCount = $clReponseXML->clGetCount()->m_nNbTotal;
        return $res;
        //return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    public function oUpdateMessage(array $requestHeaders, $xmlData) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        $aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;
        return $this->m_clSOAPProxy->updateMessage($xmlData, $aTabHeaderSuppl);
    }

    public function oUpdateMessages(array $requestHeaders, $messages, $column, $value) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        $aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;

        $updateMessages = new UpdateColumnMessageValueInBatch();
        $updateMessages->IDMessage = $messages;
        $updateMessages->Column = $column;
        $updateMessages->Value = $value;
        
        return $this->m_clSOAPProxy->updateMessages($updateMessages, $aTabHeaderSuppl);
    }

    public function oCreateMessage(array $requestHeaders, $type, $originalMessage, $templateId)
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $message = new CreateMessage();
        $message->CreateType = $type;
        $message->IDAnswerType = $templateId;
        if($originalMessage !== 'undefined')
            $message->IDMessage = $originalMessage;

        $resCreate=$this->m_clSOAPProxy->createMessage($message, $aTabHeaderSuppl);

        // depuis le schema on retrouve la liste des comptes email (adresse de retour)
        $ndSchema=$resCreate->getNodeSchema();
        $clParserXSD = new ParserXSDSchema();
        $clParserXSD->Parse(0, $ndSchema);
        $clMessageXSD=$clParserXSD->clGetStructureElement('16510');
        $clStructureColonne=$clMessageXSD->getStructureColonne('16078'); // adresse de retour
        assert($clStructureColonne->getTypeElement()==StructureColonne::TM_Combo);
        $aTabCompteEmail=$clStructureColonne->clGetRestriction()->getRestriction(ColonneRestriction::R_ENUMERATION);

        // ajoute les comptes a l'xml
        $xml=$resCreate->getNodeXML();
        $xmlEmails=$xml->addChild('emails');
        foreach( $aTabCompteEmail as $id => $sCompte)
        {
            $xmlEmail=$xmlEmails->addChild('email');
            $xmlEmail->addChild('compte', $sCompte);
            $xmlEmail->addChild('id', $id);
        }

        // compte du message
        $aElemIDCompte=$xml->id_16510->id_16078;
        if ($aElemIDCompte)
        {
            $nIDCompte=(string)$aElemIDCompte;
            // si on doit ajouter la signature
            $sSignature="";
            try{
                if ($this->bGetSiAjouteSignature($nIDCompte, $type, ($originalMessage !== 'undefined')))
                {
                    $sSignature=$this->sGetSignature($nIDCompte);
                }
            }
            catch(\Exception $e)
            {
                $sSignature="";
            }
            if ($sSignature!="")
                $xml->addChild('signature', $sSignature);
        }

        return $xml->asXML();
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function oGetReplyTemplates() {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl(array());
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $aTabHeaderSuppl[SOAPProxy::HEADER_AutoValidate]=SOAPProxy::AUTOVALIDATE_Cancel; //on ne garde pas le contexte ouvert
        $specialParamList = new SpecialParamListType();
        $specialParamList->First = 0;
        $specialParamList->Length = 50;
        $specialParamList->WithEndCalculation = 0;
        $execaction = new Execute();
        $execaction->ID = Langage::ACTION_RechercherReponseType;
        $execaction->SpecialParamList = $specialParamList;
        $oRet = $this->m_clSOAPProxy->execute($execaction, $aTabHeaderSuppl);
        if(!$oRet->sGetReturnType() === XMLResponseWS::RETURNTYPE_LIST) {
            throw new \Exception("Expected List but got " . $oRet->sGetReturnType());
        }
        return $oRet->getNodeXML()->asXML();
    }

    public function oReadMessage($messageID, array $requestParams, array $requestHeaders) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestParams);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        $aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;
        $message = new ModifyMessage();
        $message->IDMessage = $messageID;
        return $this->m_clSOAPProxy->modifyMessage($message, $aTabHeaderSuppl)->getNodeXML()->asXML();
    }

    /**
     * @param array $requestHeaders
     * @param $requestParams
     * @param $messageID
     * @return bool
     * @throws \Exception
     */
    public function oSendMessage(array $requestHeaders, $requestParams, $messageID) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);
        $asyncProp = SOAPProxy::HEADER_OptionDialogue_ListContentAsync;
        $aTabHeaderSuppl[SOAPProxy::HEADER_OptionDialogue]->$asyncProp = 0;
        $message = new SendMessage();
        $message->IDMessage = $messageID;
        $result = $this->m_clSOAPProxy->sendMessage($message, $aTabHeaderSuppl);
        if($result->sGetReturnType() !== XMLResponseWS::RETURNTYPE_EMPTY)
            throw new \Exception("Could not send message");
        return true;
    }

    public function oGetAttachment(array $requestHeaders, $messageId, $attachmentId) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);


        $getPJ = new GetPJ();
        $getPJ->IDMessage = $messageId;
        $getPJ->IDPJ = $attachmentId;

        return $this->m_clSOAPProxy->getPJ($getPJ, $aTabHeaderSuppl);
    }

    public function oPrintMessage($messageId) {
        $clIdentification = $this->_clGetIdentificationREST(null, false);

        return $this->m_clRESTProxy->sPrintMessage($messageId, $clIdentification);
    }

    /**
     * @param array $requestHeaders
     * @param $messageId
     * @param $data
     * @param $encoding
     * @param $filename
     * @param $size
     * @return string
     * @throws \Exception
     */
    public function oAddAttachment(array $requestHeaders, $messageId, $data, $encoding, $filename, $size) : string
    {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);

        $addPJ = new AddPJ();
        $dataPJ = new DataPJType();
        $dataPJ->filename = $filename;
        $dataPJ->encoding = $encoding;
        $dataPJ->size = $size;
        $dataPJ->_ = $data;
        $addPJ->IDMessage = $messageId;
        $addPJ->DataPJ = $dataPJ;

        return $this->m_clSOAPProxy->addPJ($addPJ, $aTabHeaderSuppl)->getNodeXML()->asXML();
    }

    public function oDeleteAttachment(array $requestHeaders, $messageId, $attachmentId) {
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($requestHeaders);
        $aTabHeaderSuppl = $this->_aGetTabHeader($aTabHeaderSuppl);

        $deletePJ = new DeletePJ();
        $deletePJ->IDMessage = $messageId;
        $deletePJ->IDPJ = $attachmentId;

        return $this->m_clSOAPProxy->deletePj($deletePJ, $aTabHeaderSuppl)->getNodeXML()->asXML();
    }


    public function sGetColInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption)
    {
        $clIdentification = $this->_clGetIdentificationREST('', false);

        $this->m_clRESTProxy->sGetColInRecord($sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, $clIdentification);
    }

    public function sGetSignature($compteID)
    {
        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=[];

        return $this->m_clRESTProxy->sGetColInRecord(Langage::TABL_CompteEmail, $compteID, Langage::COL_COMPTEEMAIL_Signature, $aTabParam, $aTabOption, $clIdentification);
    }

    public function bGetSiAjouteSignature($compteID, $sType, $withOriginalMessage)
    {
//        const CREATE_TYPE_EMPTY = 'Empty';
//        const CREATE_TYPE_FORWARD = 'Forward';
//        const CREATE_TYPE_ANSWER = 'Answer';
//        const CREATE_TYPE_ANSWER_ALL = 'Answer All';
//        const CREATE_TYPE_ANSWER_TYPE = 'Answer Type';

        $nIDCol=($sType=='Empty' || (($sType=='Answer Type') && !$withOriginalMessage)) ? Langage::COL_COMPTEEMAIL_SignatureNouveau : Langage::COL_COMPTEEMAIL_SignatureRepondre;

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aTabParam=[];
        $aTabOption=[];

        $sRes=$this->m_clRESTProxy->sGetColInRecord(Langage::TABL_CompteEmail, $compteID, $nIDCol, $aTabParam, $aTabOption, $clIdentification);

        return $sRes!=="Non";
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
}