<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;

use App\Service\IconManipulation;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResult;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ActionResultCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\IHMLoader;
use NOUT\Bundle\NOUTOnlineBundle\Entity\InfoIHM;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Menu\ItemMenu;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTFileInfo;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\CondType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\REST\HTTPResponse;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\GestionWSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;

/**
 * Class NOUTClient
 * @package NOUT\Bundle\NOUTOnlineBundle\Service
 */
class NOUTClientIHM extends NOUTClientBase
{
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
        if ($this->bGereWSDL(self::OPT_MenuVisible))
        {
            $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDOptionMenu, CondType::COND_MENUVISIBLE, 1);
        }
        else
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
            ->setHomeHeight(intval($objSrc->home_height))
            ->setHomeWithAlpha(boolval($objSrc->home_withalpha ?? false))
        ;

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
     * @param array  $aTabPHPManipulation
     * @param string $prefix
     * @return array
     */
    protected function _aTabPHPManipulationToOptions(array $aTabPHPManipulation, string $prefix='') : array
    {
        $aRet = [];
        foreach($aTabPHPManipulation as $name=>$option)
        {
            if (empty($prefix)){
                $option = $option['params'];
            }
            if (is_array($option))
            {
                $aRet += $this->_aTabPHPManipulationToOptions($option, "$name-");
            }
            else
            {
                $aRet[$prefix.$name]=$option;
            }
        }
        return $aRet;
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
        $clIdentification = $this->_clGetIdentificationREST('', true);
        //on veut le contenu
        $aTabOptions[RESTProxy::OPTION_WantContent] = 1;

        if (array_key_exists(RESTProxy::OPTION_Height, $aTabOptions)
            && array_key_exists(IconManipulation::PHPMANIP_NAME, $aTabPHPManipulation)
            && IconManipulation::s_bCanBeInvoked()
        ){
            $aTabPHPManipulation[IconManipulation::PHPMANIP_NAME]['params'] = IconManipulation::s_SurchargeSize($aTabOptions[RESTProxy::OPTION_Height], $aTabPHPManipulation[IconManipulation::PHPMANIP_NAME]['params']);
            unset($aTabOptions[RESTProxy::OPTION_Height]);
        }

        $aTabOptionsForName = $aTabOptions;
        if (count($aTabPHPManipulation)>0)
        {
            $aTabOptionsForName += $this->_aTabPHPManipulationToOptions($aTabPHPManipulation);
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
            $bOnManipIsOk = false;
            foreach($aTabPHPManipulation as $name=>$option)
            {
                if (empty($option['params'])){
                    continue;
                }

                if (!call_user_func($option['callback'], $option['params'], $oNOUTFileInfo))
                {
                    //on supprime l'image manipulée si elle existait déjà
                    $this->m_clCache->deleteImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptionsForName);
                    $oNOUTFileInfo->setNoCache(true);
                    return $oNOUTFileInfo;
                }
                $bOnManipIsOk = true;
            }

            if ($bOnManipIsOk && !is_null($this->m_clCache))
            {
                //on sauve l'image modifiée
                $this->m_clCache->saveImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOptionsForName, $oNOUTFileInfo);
            }
        }


        return $oNOUTFileInfo;
    }

    /**
     * @param string   $typecache
     * @param string   $key
     * @param callable $fctIfNotInCache
     * @return mixed
     */
    protected function _getFromCache(string $typecache, string $key, callable $fctIfNotInCache)
    {
        $ret = $this->fetchFromCache($typecache, $key);
        if (!isset($ret) || ($ret === false)){
            //pas dans le cache
            $ret = $fctIfNotInCache();
            $this->_saveInCache($typecache, $key, $ret);
        }
        return $ret;
    }


    /**
     * @return ActionResult
     * @throws \Exception
     */
    protected function _getWeekSchedule() : ActionResult
    {
        $TabHeaderQuery = array(
            SOAPProxy::HEADER_APIUser => 1,
            SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel,
        );

        //--------------------------------------------------------------------------------------------
        // Paramètres
        $clParam = $this->_oGetParam(Execute::class, []);
        $clParam->ID = Langage::ACTION_Horaires_Ouverture;             // identifiant de l'action (String)
        $clParam->Final = 0;

        //--------------------------------------------------------------------------------------------
        // Headers
        $aTabHeaderSuppl = $this->_aGetHeaderSuppl($TabHeaderQuery, '');
        $clReponseXML = $this->m_clSOAPProxy->execute($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getWeekSchedule() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Session, "info_week_schedule", function(){
            $oRet = $this->_getWeekSchedule();

            /** @var RecordCache $records */
            $records = $oRet->getData()->getRecordCache()->getMapIDTableauIDEnreg2Record();
            $days = array(
                Langage::JS_Dimanche,
                Langage::JS_Lundi,
                Langage::JS_Mardi,
                Langage::JS_Mercredi,
                Langage::JS_Jeudi,
                Langage::JS_Vendredi,
                Langage::JS_Samedi,
            );
            $week = array();
            foreach($records as $record) {
                /** @var Record $record */
                if(($dayNumber = array_search($record->getValCol(Langage::COL_HORAIREOUVERTURE_JourSemaine), $days)) !== false) {
                    $day = new \stdClass();
                    $day->openingTime = $record->getValCol(Langage::COL_HORAIREOUVERTURE_HeureDeb);
                    $day->closingTime = $record->getValCol(Langage::COL_HORAIREOUVERTURE_HeureFin);
                    $week[$dayNumber] = $day;
                }
            }
            return $week;
        });
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function _getFunctionList()
    {
        $clIdentification = new Identification();
        $clIdentification->m_clUsernameToken = $this->_oGetNCSUsernameToken();

        $clIdentification->m_sAuthToken = $this->m_clRESTProxy->sGenerateAuthTokenForApp($clIdentification);
        $clIdentification->m_bAPIUser = true;
        $clIdentification->m_clUsernameToken = null;

        return $this->m_clRESTProxy->oGetFunctionsList($clIdentification);
    }
    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFunctionList()
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_NOUTOnline, "function_list", function(){
            return $this->_getFunctionList();
        });
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    protected function _getFormuleHighlighter()
    {
        $clIdentification = new Identification();
        $clIdentification->m_clUsernameToken = $this->_oGetNCSUsernameToken();

        $clIdentification->m_sAuthToken = $this->m_clRESTProxy->sGenerateAuthTokenForApp($clIdentification);
        $clIdentification->m_bAPIUser = true;
        $clIdentification->m_clUsernameToken = null;

        return $this->m_clRESTProxy->oGetFormuleHighlighter($clIdentification);
    }
    /**
     * @return mixed
     * @throws \Exception
     */
    public function getFormuleHighlighter()
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_NOUTOnline, "formule_highlighter", function(){
            return $this->_getFormuleHighlighter();
        });
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function _getModelList()
    {
        $clIdentification = new Identification();
        $clIdentification->m_clUsernameToken = $this->_oGetNCSUsernameToken();

        $clIdentification->m_sAuthToken = $this->m_clRESTProxy->sGenerateAuthTokenForApp($clIdentification);
        $clIdentification->m_bAPIUser = true;
        $clIdentification->m_clUsernameToken = null;

        return $this->m_clRESTProxy->oGetModelList($clIdentification);
    }

    /**
     * @param \stdClass $info
     * @return \stdClass
     */
    protected function __initFromJson(\stdClass $info) : \stdClass
    {
        $obj = new \stdClass();
        $obj->id = $info->id;
        $obj->title = $info->title;

        if (property_exists($info->columns, Langage::COL_GENERIQUE_TYPEFORMULAIRE)){
            $info_typecol = $info->columns->{Langage::COL_GENERIQUE_TYPEFORMULAIRE};
            $obj->type = new \stdClass();
            $obj->type->id = $info_typecol->value;
            $obj->type->title = $info_typecol->displayValue;
        }

        return $obj;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getModelList() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "model_list", function(){
            $oRestResponse = $this->_getModelList();
            $aReturnFinal=[];
            foreach($oRestResponse->result as $idObg)
            {
                $info = $oRestResponse->values->elements->$idObg;

                $obj = $this->__initFromJson($info);

                //le type bdd si pertinant
                if (property_exists($info->columns, Langage::COL_MODELECLASSIQUE_TypeModele)) {
                    $obj->type->bdd = new \stdClass();
                    $obj->type->bdd->title = $info->columns->{Langage::COL_MODELECLASSIQUE_TypeModele}->displayValue;
                    $obj->type->bdd->id = $info->columns->{Langage::COL_MODELECLASSIQUE_TypeModele}->value;
                }

                $aReturnFinal[$obj->id]=$obj;
            }
            return $aReturnFinal;
        });
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    protected function _getColumnList()
    {
        $clIdentification = new Identification();
        $clIdentification->m_clUsernameToken = $this->_oGetNCSUsernameToken();

        $clIdentification->m_sAuthToken = $this->m_clRESTProxy->sGenerateAuthTokenForApp($clIdentification);
        $clIdentification->m_bAPIUser = true;
        $clIdentification->m_clUsernameToken = null;

        return $this->m_clRESTProxy->oGetColumnList($clIdentification);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function getColumnList()
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "column_list", function(){
            $aModeles = $this->getModelList();
            $oRestResponse = $this->_getColumnList();

            //il faut que je fasse un truc du retour,
            $aReturnFinal=[];
            foreach($oRestResponse->result as $idObj)
            {
                $info = $oRestResponse->values->elements->$idObj;

                $obj = $this->__initFromJson($info);
                $obj->title = $info->columns->{Langage::COL_COLONNE_Libelle}->displayValue;

                if (property_exists($info->columns, Langage::COL_COLLIBELLE_IDNiveau)) {
                    $obj->type->niveau = $info->columns->{Langage::COL_COLLIBELLE_IDNiveau}->value;
                }

                $info_tableau = $info->columns->{Langage::COL_COLONNE_IDTableau};
                $obj->form = new \stdClass();
                $obj->form->id = $info_tableau->value;
                $obj->form->title = $info_tableau->displayValue;

                $info_modele = $info->columns->{Langage::COL_COLONNE_IDModele};
                if (!empty($info_modele->value)){
                    if (array_key_exists($info_modele->value, $aModeles)){
                        //on connait le modèle
                        $obj->modele = $aModeles[$info_modele->value];
                    }
                    else {
                        //on prend ce qu'on a dans la description de la colonne
                        $obj->modele = new \stdClass();
                        $obj->modele->title = $info_modele->displayValue;
                        $obj->modele->id = $info_modele->value;
                    }
                }

                $obj->ordre = $info->columns->{Langage::COL_COLONNE_Ordre}->value;
                $aReturnFinal[$obj->id]=$obj;
            }
            return $aReturnFinal;
        });
    }


    /**
     * @param $idtableau
     * @return mixed
     * @throws \Exception
     */
    public function getTableColumnList($idtableau)
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "column_list_$idtableau", function() use ($idtableau) {

            $column_list = $this->getColumnList();
            $filtered = array_filter($column_list, function($item) use ($idtableau) {
                return $item->form->id == $idtableau;
            });

            usort($filtered, function($item1, $item2){
                if ($item1->ordre == $item2->ordre) {
                    return 0;
                }
                return ($item1->ordre < $item2->ordre) ? -1 : 1;
            });
            return $filtered;
        });
    }


    /**
     * @return mixed
     * @throws \Exception
     */
    protected function __getBaseTableList()
    {
        $clIdentification = new Identification();
        $clIdentification->m_clUsernameToken = $this->_oGetNCSUsernameToken();

        $clIdentification->m_sAuthToken = $this->m_clRESTProxy->sGenerateAuthTokenForApp($clIdentification);
        $clIdentification->m_bAPIUser = true;
        $clIdentification->m_clUsernameToken = null;

        return $this->m_clRESTProxy->oGetBaseTableList($clIdentification);
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getBaseTableListWithoutColumns() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "base_table_list_without_columns", function(){
            $oRestResponse = $this->__getBaseTableList();
            $aReturnFinal = [];

            foreach($oRestResponse->result as $idObj)
            {
                $info = $oRestResponse->values->elements->$idObj;

                $obj = $this->__initFromJson($info);

                $aReturnFinal[$obj->id]=$obj;
            }
            return $aReturnFinal;
        });
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getBaseTableListWithColumns() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "base_table_list_with_columns", function(){

            $aReturnFinal = $this->_getBaseTableListWithoutColumns();
            array_walk($aReturnFinal, function($form){
                $form->columns=[];
            });

            $aColonnes = $this->getColumnList();
            foreach($aColonnes as $colonne)
            {
                if (array_key_exists($colonne->form->id, $aReturnFinal)){
                    $form = $aReturnFinal[$colonne->form->id];
                }
                else {
                    $form = new \stdClass();
                    $aReturnFinal[$colonne->form->id] = $form;

                    $form->title = $colonne->form->title;
                    $form->id = $colonne->form->id;
                    $form->columns = [];
                }
                $form->columns[$colonne->ordre]=$colonne;
            }
            array_walk($aReturnFinal, function($form){
                ksort($form->columns);
            });

            return $aReturnFinal;

        });
    }


    /**
     * @param bool $bWithColumns
     * @return array
     * @throws \Exception
     */
    public function getBaseTableList(bool $bWithColumns=true) : array
    {
        if ($bWithColumns){
            return $this->_getBaseTableListWithColumns();
        }

        return $this->_getBaseTableListWithoutColumns();
    }


    /**
     * @return array
     * @throws \Exception
     */
    protected function _getFormTableListWithoutColumns() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "form_table_list_without_columns", function(){
            $aReturnFinal = $this->_getBaseTableListWithoutColumns();
            $aReturnFinal = array_filter($aReturnFinal, function($form){
                return property_exists($form, 'type') && ($form->type->id==Langage::TABL_Tableau);
            });
            return $aReturnFinal;
        });
    }

    /**
     * @return array
     * @throws \Exception
     */
    protected function _getFormTableListWithColumns() : array
    {
        return $this->_getFromCache(NOUTClientCache::CACHE_Language, "form_table_list_with_columns", function(){
            $aReturnFinal = $this->_getBaseTableListWithColumns();
            $aReturnFinal = array_filter($aReturnFinal, function($form){
                return property_exists($form, 'type') && ($form->type->id==Langage::TABL_Tableau);
            });
            return $aReturnFinal;
        });
    }

    /**
     * @param bool $bWithColumns
     * @return array
     * @throws \Exception
     */
    public function getFormTableList(bool $bWithColumns=true) : array
    {
        if ($bWithColumns){
            return $this->_getFormTableListWithColumns();
        }

        return $this->_getFormTableListWithoutColumns();
    }




}