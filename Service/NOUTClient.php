<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\ContextsBundle\Service;

use NOUT\Bundle\ContextsBundle\Entity\ActionResult;
use NOUT\Bundle\ContextsBundle\Entity\ActionResultCache;
use NOUT\Bundle\ContextsBundle\Entity\ConnectionInfos;
use NOUT\Bundle\ContextsBundle\Entity\IHMLoader;
use NOUT\Bundle\ContextsBundle\Entity\Menu\ItemMenu;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionOperateur;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\Count;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ParserRecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\GestionWSDL;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

class NOUTClient
{
	/**
	 * @var ConfigurationDialogue
	 */
	private $m_clConfigurationDialogue;

	/**
	 * @var string
	 */
	private $m_sCacheDir;

	/**
	 * @var SOAPProxy
	 */
	private $m_clSOAPProxy;

	/**
	 * @Var RESTProxy
	 */
	private $m_clRESTProxy;


	/**
	 * @var TokenStorage
	 */
	private $__tokenStorage;

	/**
	 * @var NOUTCache
	 */
	private $m_clCacheSession = null;

    /**
     * @var NOUTCache
     */
    private $m_clCacheIHM = null;

	/**
	 * @param TokenStorage          $security
	 * @param OnlineServiceFactory  $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 * @param                       $sCacheDir
	 * @throws \Exception
	 */
	public function __construct(TokenStorage $tokenStorage, OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue, $sCacheDir)
	{
		$this->__tokenStorage = $tokenStorage;

		$this->m_sCacheDir   = $sCacheDir.'/'.self::REPCACHE;


		$oSecurityToken = $this->_oGetToken();

		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
		$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

		$this->m_clConfigurationDialogue = $configurationDialogue;

		//création du cache pour la session
        if ($oSecurityToken instanceof NOUTToken)
        {
            $sSessionToken = $oSecurityToken->getSessionToken();
            $this->m_clCacheSession = new NOUTCache($this->m_sCacheDir, $sSessionToken);

            $sRepCacheIHM = $this->_sGetRepCacheIHM(Langage::TABL_ImageCatalogue);
            $this->m_clCacheIHM = new NOUTCache($sRepCacheIHM);
        }
	}

	/**
	 * @return NOUTToken
	 */
	protected function _oGetToken()
	{
		return $this->__tokenStorage->getToken();
	}


	protected function _TestParametre($sTypeTest, $sNomParametre, $sValeurParametre, $ValeurTest)
	{
		switch($sTypeTest)
		{
		case self::TP_NotEmpty:
			if (empty($sValeurParametre))
			{
				throw new \Exception('the value of the parameter '.$sNomParametre.' must not be empty.');
			}
			break;

		case self::TP_InArray:
			if (!in_array($sValeurParametre, $ValeurTest))
			{
				$sMessage = 'the value of the parameter '.$sNomParametre.' must be one of : ';
				foreach($ValeurTest as $Value)
				{
					$sMessage.=$Value.', ';
				}
				rtrim($sMessage, ", ");
				$sMessage.='.';

				throw new \Exception($sMessage);
			}
			break;
		}
	}


	protected function _ThrowError($sMessage, $nCode=0)
	{
		throw new \Exception($sMessage, $nCode);
	}

	/**
	 * récupère le numéro de version
	 * @return string
	 */
	public function sGetVersion()
	{
		return $this->m_clRESTProxy->sGetVersion();
	}

	/**
	 * @return NOUTCache
	 */
	public function getCacheSession()
	{
		return $this->m_clCacheSession;
	}

	/**
	 * @return string
	 */
	public function getTimeZone()
	{
		return $this->_oGetToken()->getTimeZone();
	}

	/**
	 * retourne les options de dialogue
	 * @return OptionDialogue
	 */
	protected function _clGetOptionDialogue()
	{
		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->InitDefault();
		$clOptionDialogue->DisplayValue = OptionDialogue::DISPLAY_None;
		$clOptionDialogue->LanguageCode = $this->m_clConfigurationDialogue->getLangCode();

		return $clOptionDialogue;
	}

	/**
	 * retourne une classe qui contient les informations de connexions
	 *
	 * @return ConnectionInfos
	 */
	public function getConnectionInfos()
	{
		$oToken = $this->_oGetToken();
		return new ConnectionInfos($oToken->getLoginSIMAX(), $oToken->isExtranet(), $oToken->getLoginExtranet());
	}


    /**
     * @param NOUTToken $oToken
     * @return UsernameToken
     */
    protected function _oGetUsernameToken(NOUTToken $oToken)
    {
        $oUsernameToken = new UsernameToken(
            $oToken->getLoginSIMAX(),
            $oToken->getPasswordSIMAX(), //le mot de passe n'est pas stocké dans le user
            $this->m_clConfigurationDialogue->getModeAuth(),
            $this->m_clConfigurationDialogue->getSecret()
        );

        return $oUsernameToken;
    }

    /**
     * @param NOUTToken $oToken
     * @return array|UsernameToken
     */
    protected function _oGetUsernameTokenSOAP($oToken)
    {
        $oUsernameToken = $this->_oGetUsernameToken($oToken);
        return $this->m_clSOAPProxy->getUsernameTokenForWdsl($oUsernameToken);
    }

	/**
	 * @param $sIDContexteAction
	 * @param $bAPIUser
	 * @return Identification
	 */
	protected function _clGetIdentificationREST($sIDContexteAction, $bAPIUser)
	{
		$clIdentification = new Identification();

		// récupération de l'utilsateur connecté
		$oToken = $this->_oGetToken();

		$clIdentification->m_clUsernameToken   = $this->_oGetUsernameToken($oToken);
		$clIdentification->m_sTokenSession     = $oToken->getSessionToken();
		$clIdentification->m_sIDContexteAction = $sIDContexteAction;
		$clIdentification->m_bAPIUser          = $bAPIUser;

		return $clIdentification;
	}


	/**
	 * @param array $aHeaderSup
	 * @return array
	 */
	protected function _aGetTabHeader(array $aHeaderSup = null)
	{
		// récupération de l'utilsateur connecté
		$oToken = $this->_oGetToken();

		$aTabHeader = array(
			SOAPProxy::HEADER_UsernameToken  => $this->_oGetUsernameTokenSOAP($oToken),
			SOAPProxy::HEADER_SessionToken   => $oToken->getSessionToken(),
			SOAPProxy::HEADER_OptionDialogue => $this->_clGetOptionDialogue(),
		);


		if (!empty($aHeaderSup))
		{
			$aTabHeader = array_merge($aTabHeader, $aHeaderSup);
		}

		return $aTabHeader;
	}

	/**
	 * @param string $sIDform identifiant du formulaire
	 * @param ConditionFileNPI $clFileNPI condition pour la requete
	 * @param array $TabColonneAff tableau des colonnes a afficher
	 * @param array $TabHeaderSuppl tableau des headers
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	protected function _oRequest($sIDform, ConditionFileNPI $clFileNPI, array $TabColonneAff, array $TabHeaderSuppl)
	{
		$clParamRequest           = new Request();
		$clParamRequest->Table    = $sIDform;
		$clParamRequest->CondList = $clFileNPI->sToSoap();
		$clParamRequest->ColList  = new ColListType($TabColonneAff);

		return $this->m_clSOAPProxy->request($clParamRequest, $this->_aGetTabHeader($TabHeaderSuppl));
	}


    /**
     * récupère la liste des icones avec une grosse image
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
     */
    protected function _oGetTabIcon($idCol)
    {
        $aTabColonne = array();

        $clFileNPI = new ConditionFileNPI();
        $clFileNPI->EmpileCondition($idCol, ConditionColonne::COND_DIFFERENT, '');


        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel,  //on ne garde pas le contexte ouvert
        );

        return $this->_oRequest(Langage::TABL_ImageCatalogue, $clFileNPI, $aTabColonne, $aTabHeaderSuppl);
    }


	/**
	 * récupère la liste des options de menu sur les actions accordées par les droits et les séparateurs
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
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
		$clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDAction, ConditionColonne::COND_EQUAL, '');
		$clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_Libelle, ConditionColonne::COND_EQUAL, '-');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_Commande, ConditionColonne::COND_EQUAL, '');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);

        //les options de menu sur lesquelles les droits sont accordés
        if ($this->m_clSOAPProxy->getGestionWSDL()->bGere(GestionWSDL::OPT_MenuVisible))
        {
            $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDOptionMenu, ConditionColonne::COND_MENUVISIBLE, 1);
        }
        else
        {
            $clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDAction, ConditionColonne::COND_WITHRIGHT, 1);
        }
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$aTabHeaderSuppl = array(
			SOAPProxy::HEADER_AutoValidate => SOAPProxy::AUTOVALIDATE_Cancel,  //on ne garde pas le contexte ouvert
		);

		return $this->_oRequest(Langage::TABL_OptionMenuPourTous, $clFileNPI, $aTabColonne, $aTabHeaderSuppl);
	}



	/**
	 * récupère la liste des menus
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
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
			SOAPProxy::HEADER_APIUser      => SOAPProxy::APIUSER_Active,           //on force l'utilisation de l'user d'application (max) car un utilisateur classique n'aura pas les droit d'executer cette requete
		);

		return $this->_oRequest(Langage::TABL_MenuPourTous, new ConditionFileNPI(), $aTabColonne, $aTabHeaderSuppl);
	}

    /**
     * récupère les infos du menu
     */
    protected function _oGetInfoIHM()
    {
        $sUsername = $this->_oGetToken()->getLoginSIMAX();
        if (!is_null($this->m_clCacheIHM))
        {
            $oInfoIHM = $this->m_clCacheIHM->fetch('info_'.$sUsername);
            if ($oInfoIHM !== false){
                return $oInfoIHM; //on a déjà les infos du menu
            }
        }

        //on a pas les infos, il faut les calculer
        $clReponseXML_OptionMenu    = $this->_oGetTabMenu_OptionMenu();
        $clReponseXML_Menu          = $this->_oGetTabMenu_Menu();
        $clReponseXML_SmallIcon     = $this->_oGetTabIcon(Langage::COL_IMAGECATALOGUE_Image);
        $clReponseXML_BigIcon       = $this->_oGetTabIcon(Langage::COL_IMAGECATALOGUE_ImageGrande);

        $clIHMLoader = new IHMLoader($clReponseXML_OptionMenu, $clReponseXML_Menu, $clReponseXML_SmallIcon, $clReponseXML_BigIcon);
        $oInfoIHM = $clIHMLoader->oGetInfoIHM();
        $this->m_clCacheIHM->save('info_'.$sUsername, $oInfoIHM);

        return $oInfoIHM;
    }

    /**
     * récupère les infos du menu
     */
    protected function __oGetIHMPart($method, $prefix)
    {
        $sUsername = $this->_oGetToken()->getLoginSIMAX();
        if (!is_null($this->m_clCacheIHM))
        {
            $aTabMenu = $this->m_clCacheIHM->fetch("info_{$prefix}_{$sUsername}");
            if ($aTabMenu !== false){
                return $aTabMenu; //on a déjà les infos du menu
            }
        }

        $clIdentification = $this->_clGetIdentificationREST('', false);

        $aInfo = array();
        //on a pas les infos, il faut les calculer
        $json = json_decode( $this->m_clRESTProxy->$method($clIdentification));
        if (is_array($json) && (count($json)>0))
        {
            foreach($json as $objet)
            {
                $itemMenu = $this->__oGetItemMenu($objet);
                $aInfo[]=$itemMenu;
            }
        }

        $this->m_clCacheIHM->save("info_{$prefix}_{$sUsername}", $aInfo);
        return $aInfo;
    }

    /**
     * récupère les infos du menu
     * @param \stdClass $objSrc
     * @return ItemMenu
     */
    protected function __oGetItemMenu($objSrc)
    {
        $itemMenu = new ItemMenu($objSrc->id, $objSrc->title, $objSrc->is_menu_option);
        $itemMenu
            ->setSeparator($objSrc->is_separator)
            ->setRootMenu($objSrc->is_root)
            ->setIdAction($objSrc->idaction)
            ->setCommand($objSrc->command)

            ->setIconBig($objSrc->icon_big)
            ->setIconSmall($objSrc->icon_small)

            ->setHomeWithImg($objSrc->home_withimg)
            ->setHomeDesc($objSrc->home_desc)
            ->setHomeTitle($objSrc->home_title)

            ->setHomeWidth($objSrc->home_width)
            ->setHomeHeight($objSrc->home_height)
        ;

        if (count($objSrc->tab_options)>0)
        {
            foreach($objSrc->tab_options as $objChild)
            {
                $childMenu = $this->__oGetItemMenu($objChild);
                $itemMenu->AddOptionMenu($childMenu);
            }
        }

        return $itemMenu;
    }

    /**
     * @param $member_name
     * @param $method_name
     * @param $prefix
     * @return ActionResult
     */
    protected function _oGetIHMPart($member_name, $method_name, $prefix)
    {
        $clActionResult = new ActionResult(null);
        if (!$this->_oGetToken()->isVersionSup('1637.02'))
        {
            //l'ancien système
            $oInfoMenu = $this->_oGetInfoIHM();
            $clActionResult->setData($oInfoMenu->$member_name);
        }
        else
        {
            $tabMenu = $this->__oGetIHMPart($method_name, $prefix);
            $clActionResult->setData($tabMenu);
        }

        //le menu dépend de l'utilisateur, c'est un cache privé
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);
        return $clActionResult;

    }

    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     */
    public function getTabMenu()
    {
        return $this->_oGetIHMPart('aMenu','sGetMenu', 'menu');
    }

    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     */
    public function getCentralIcon()
    {
        return $this->_oGetIHMPart('aBigIcon','sGetCentralIcon', 'home');
    }

    /**
     * retourne un tableau d'option de menu
     * @return ActionResult
     */
    public function getToolbar()
    {
        return $this->_oGetIHMPart('aToolbar','sGetToolbar', 'toolbar');
    }

	/**
	 * @param $sIDTab
	 * @return string
	 */
	protected function _sGetRepCacheIHM($sIDTab)
	{
		$oToken    = $this->_oGetToken();
		$clLangage = $oToken->getLangage();

		$sRep = $this->m_sCacheDir.'/'.self::REPCACHE_IHM.'/'.$clLangage->getVersionLangage();

		switch ($sIDTab)
		{
		case Langage::TABL_ImageCatalogue:
			$sRep .=	'/'.$clLangage->getVersionIcone();
			break;
		}

		if (!file_exists($sRep))
		{
			mkdir($sRep, 0777, true);
		}

		return $sRep;
	}


    // Pour l'écriture des fichiers en cache
    /**
     * @return string
     */
    protected function _sGetRepCacheUpload()
    {
        $oToken    = $this->_oGetToken();
        $clLangage = $oToken->getLangage();

        $sRep = $this->m_sCacheDir.'/'.self::REPCACHE_UPLOAD.'/'.$clLangage->getVersionLangage();


        if (!file_exists($sRep))
        {
            mkdir($sRep, 0777, true);
        }

        return $sRep;
    }

	/**
	 * retourne le nom de fichier pour le cache
	 * @param $sIDTab
	 * @param $sIDElement
	 * @param $aTabOption array
	 */
	protected function _sGetNomFichierCacheIHM($sIDTab, $sIDElement, $aTabOption)
	{
		$sRep = $this->_sGetRepCacheIHM($sIDTab);

		//on tri le tableau pour toujours l'avoir dans le même ordre
		ksort($aTabOption);

		$sListeOption = '';
		foreach ($aTabOption as $sOption => $valeur)
		{
			if (!empty($sListeOption))
			{
				$sListeOption .= '_';
			}

			$sListeOption .= $valeur;
		}

		return $sRep.'/'.$this->_sSanitizeFilename($sIDElement.'_'.$sListeOption);
	}

    /**
     * retourne le nom de fichier pour le cache
     * @param $sIDTab
     * @param $sIDElement
     * @param $aTabOption array
     */
    protected function _sGetCacheFilePath($sIDElement, $aTabOption)
    {
        $sRep = $this->_sGetRepCacheUpload();

        //on tri le tableau pour toujours l'avoir dans le même ordre
        ksort($aTabOption);

        $sListeOption = '';
        foreach ($aTabOption as $sOption => $valeur)
        {
            if (!empty($sListeOption))
            {
                $sListeOption .= '_';
            }

            $sListeOption .= $valeur;
        }

        // return $sRep.'/'.$this->_sSanitizeFilename($sIDElement.'_'.$sListeOption); // Si on veut un nom avec options
        return $sRep.'/'.$this->_sSanitizeFilename($sIDElement); // Nom sans les options
    }



	/**
	 * @param $filename
	 * @return string
	 */
	protected function _sSanitizeFilename($filename)
	{
		// a combination of various methods
		// we don't want to convert html entities, or do any url encoding
		// we want to retain the "essence" of the original file name, if possible
		// char replace table found at:
		// http://www.php.net/manual/en/function.strtr.php#98669
		$replace_chars = array(
			'Š' => 'S', 'š' => 's', 'Ð' => 'Dj','Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
			'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
			'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
			'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss','à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
			'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
			'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
			'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
		);
		$filename = strtr($filename, $replace_chars);
		// convert & to "and", @ to "at", and # to "number"
		$filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
		$filename = preg_replace('/[^(\x20-\x7F)]*/', '', $filename); // removes any special chars we missed
		$filename = str_replace(' ', '-', $filename); // convert space to hyphen
		$filename = str_replace('/', '-', $filename); // convert / to hyphen
		$filename = str_replace('\\', '-', $filename); // convert \ to hyphen
		$filename = str_replace('\'', '', $filename); // removes apostrophes
		$filename = preg_replace('/[^\w\-\.]+/', '', $filename); // remove non-word chars (leaving hyphens and periods)
		$filename = preg_replace('/[\-]+/', '-', $filename); // converts groups of hyphens into one
		return strtolower($filename);
	}

    /**
     * Convertit un tableau de paramètres en string pour l'API
     * @param array $aTabParam
     */
    protected function _sParamArray2ParamString(array $aTabParam = array())
    {
        $XMLString = '';

        foreach ($aTabParam as $key => $value)
        {
            $XMLString .= '<id_'.$key.'>';
            $XMLString .= $value;
            $XMLString .= '</id_'.$key.'>';
        }

        return $XMLString;
    }

    /**
     * initialise la struture de paramètre a partir du tableau des paramètres de la requête HTTP
     * @param $oParam
     * @param $aTabParamRequest
     */
    protected function _initStructParamFromTabParamRequest($oParam, $aTabParamRequest)
    {
        foreach($aTabParamRequest as $property=>$valeur)
        {
            if (property_exists($oParam, $property))
            {
                $oParam->$property = $valeur;
            }
        }

        if (property_exists($oParam, self::PARAM_SPECIALPARAMLIST) && is_null($oParam->{self::PARAM_SPECIALPARAMLIST}))
        {
            $oParam->{self::PARAM_SPECIALPARAMLIST}                = new SpecialParamListType();
            $oParam->{self::PARAM_SPECIALPARAMLIST}->initFirstLength();
        }

        if (property_exists($oParam, self::PARAM_PARAMXML) && is_null($oParam->{self::PARAM_PARAMXML}))
        {
            $oParam->{self::PARAM_PARAMXML}='';
        }

    }

	/**
	 * Execute une action via son id
	 * @param string $sIDAction
	 * @param string $sIDContexte
	 * @param string $tabParamQuery
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecIDAction(array $tabParamQuery, $sIDAction, $sIDContexte = '')
	{
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
        $clParamExecute = new Execute();
        $this->_initStructParamFromTabParamRequest($clParamExecute, $tabParamQuery);

        $clParamExecute->ID           = (string)$sIDAction;             // identifiant de l'action (String)
        //--------------------------------------------------------------------------------------------

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        return $this->_oExecute($clParamExecute, $aTabHeaderSuppl);
	}



    /**
     * Execute une action via sa phrase
     * @param string $sPhrase
     * @param string $sIDContexte
     * @param string $tabParamQuery
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
     */
    public function oExecSentence(array $tabParamQuery, $sPhrase, $sIDContexte = '')
    {
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
        $clParamExecute = new Execute();
        $this->_initStructParamFromTabParamRequest($clParamExecute, $tabParamQuery);

        $clParamExecute->Sentence           = (string)$sPhrase;
        //--------------------------------------------------------------------------------------------

        //header
        $aTabHeaderSuppl = array();
        if (!empty($sIDContexte))
        {
            $aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
        }

        return $this->_oExecute($clParamExecute, $aTabHeaderSuppl);
    }



	/**
	 * Affichage d'une liste
	 * @param $sIDTableau
	 * @param string $sIDContexte
	 * @param array $tabParamQuery
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecList(array $tabParamQuery, $sIDTableau, $sIDContexte = '')
	{
		//paramètre de l'action liste
		$clParamListe     = new ListParams();
        $this->_initStructParamFromTabParamRequest($clParamListe, $tabParamQuery);
        $clParamListe->Table = $sIDTableau;

		//header
		$aTabHeaderSuppl = array();
		if (!empty($sIDContexte))
		{
			$aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
		}

        $clReponseXML = $this->m_clSOAPProxy->listAction($clParamListe, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
	}

	/**
	 * @param Execute $clParamExecute
	 * @param array   $aTabHeaderSuppl
	 * @return ActionResult
	 */
	protected function _oExecute(Execute $clParamExecute, array $aTabHeaderSuppl)
	{
		$clReponseXML = $this->m_clSOAPProxy->execute($clParamExecute, $this->_aGetTabHeader($aTabHeaderSuppl));

		return $this->_oGetActionResultFromXMLResponse($clReponseXML);
	}

	/**
	 * @param XMLResponseWS $clReponseXML
     * @param $ReturnTypeForce
     * @param $autreInfos - informations nécessaire pour le force return type
	 * @return ActionResult
	 * @throws \Exception
	 */
	protected function _oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML, $ReturnTypeForce=null, $autreInfos=null)
	{
		$clActionResult = new ActionResult($clReponseXML);
        if (!empty($ReturnTypeForce)){
            $clActionResult->ReturnType=$ReturnTypeForce; //on force le return type
        }

		switch ($clActionResult->ReturnType)
		{
            case XMLResponseWS::RETURNTYPE_EMPTY:
                break; //on ne fait rien de plus

            case XMLResponseWS::RETURNTYPE_VALUE:
            case XMLResponseWS::RETURNTYPE_REQUESTFILTER:
            case XMLResponseWS::RETURNTYPE_CHART:
            case XMLResponseWS::RETURNTYPE_NUMBEROFCHART:

            case XMLResponseWS::RETURNTYPE_XSD:
            case XMLResponseWS::RETURNTYPE_IDENTIFICATION:
            case XMLResponseWS::RETURNTYPE_PLANNING:
            case XMLResponseWS::RETURNTYPE_GLOBALSEARCH:
            case XMLResponseWS::RETURNTYPE_LISTCALCULATION:
            case XMLResponseWS::RETURNTYPE_EXCEPTION:

            case XMLResponseWS::RETURNTYPE_PRINTTEMPLATE:

            case XMLResponseWS::RETURNTYPE_MAILSERVICERECORD:
            case XMLResponseWS::RETURNTYPE_MAILSERVICELIST:
            case XMLResponseWS::RETURNTYPE_MAILSERVICESTATUS:
            case XMLResponseWS::RETURNTYPE_WITHAUTOMATICRESPONSE:
            {
                throw new \Exception("Type de retour $clActionResult->ReturnType non géré", 1);
            }

			case XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION:
			{
                // Instance d'un parseur
                $clResponseParser = new ReponseWSParser();

                /** @var ParserList $clParser */
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                $list = $clParser->getList($clReponseXML);

				$clActionResult->setData($list);
                break;
			}

            case XMLResponseWS::RETURNTYPE_REPORT:
            {
                $clActionResult->setData($clReponseXML->sGetReport());
                $clActionResult->setElement($clReponseXML->clGetElement());

                break;
            }

            case XMLResponseWS::RETURNTYPE_VALIDATERECORD:
            case XMLResponseWS::RETURNTYPE_RECORD:
			case XMLResponseWS::RETURNTYPE_VALIDATEACTION:
            {
                // Instance d'un parseur
                $clResponseParser = new ReponseWSParser();
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                /** @var ParserRecordList $clParser*/
                $clActionResult->setData($clParser->getRecord($clReponseXML));
                $clActionResult->setValidateError($clReponseXML->getValidateError());

                break;
            }

            case XMLResponseWS::RETURNTYPE_LIST:
            {
                // Bug dans InitFromXmlXsd si trop volumineux
                // OutOfMemoryException in ParserRecordList.php line 183:
                // Error: Allowed memory size of 134217728 bytes exhausted (tried to allocate 262144 bytes)

                /** @var Count $totalElements */
				$totalElements = $clReponseXML->clGetCount();

                // Par sécurité quand on affiche une liste
                if($totalElements->m_nNbDisplay > NOUTClient::MaxEnregs)
                {
                    //@@@ TODO trad
                    throw new \Exception("Votre requête a renvoyé trop d'éléments. Contactez l'éditeur du logiciel", OnlineError::ERR_MEMORY_OVERFLOW);
                }

                // Instance d'un parseur
                $clResponseParser = new ReponseWSParser();

                /** @var ParserList $clParser */
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML);

                // dump($clParser);
                // clParser est bien du type ParserList mais n'a pas encore les données

                // getList renvoit un RecordList
                $list = $clParser->getList($clReponseXML);
                // dump($list);

                $clActionResult
                    ->setData($list)
                    ->setValidateError($clReponseXML->getValidateError())
                    ->setCount($clReponseXML->clGetCount());

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
				// TODO faire la vraie messageBox

                // On fabrique la messageBox avec les données XML
                $clActionResult->setData($clReponseXML->clGetMessageBox());

                break;
            }


            /**
             * cas particulier, ici on triche
             */
            case XMLResponseWS::RETURNTYPE_COLINRECORD:
            {
                $clResponseParser = new ReponseWSParser();
                $clParser = $clResponseParser->InitFromXmlXsd($clReponseXML, $clActionResult->ReturnType, $autreInfos);

                $data = new \stdClass();
                $data->cache = $clParser->getListFullCache();
                $data->value = $clReponseXML->getData();

                $clActionResult->setData($data);

                break;
            }

		}

		return $clActionResult;
	}


    /**
     * @param $sIDContexte
     * @param Record $clRecord
     * @param int $autovalidate
     * @return ActionResult
     * @throws \Exception
     */
    public function oUpdate($sIDContexte, Record $clRecord, $autovalidate=SOAPProxy::AUTOVALIDATE_None)
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_InArray, '$autovalidate', $autovalidate, array(SOAPProxy::AUTOVALIDATE_None, SOAPProxy::AUTOVALIDATE_Cancel, SOAPProxy::AUTOVALIDATE_Validate));
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $sIDForm = $clRecord->getIDTableau();
        $sIDEnreg = $clRecord->getIDEnreg();

        $clParamUpdate              = new Update();
        $clParamUpdate->Table       = $clRecord->getIDTableau();
        $clParamUpdate->ParamXML    = "<id_$sIDForm>$sIDEnreg</id_$sIDForm>";
        // -----------------------------------------------------
        // Fichiers

        // Chercher tous les fichiers modifiés dans le Record // similaire à getStructforUpdateSOAP => getColonneFileModified
        $aFilesToSend = $this->_getModifiedFiles($clRecord);
        // -----------------------------------------------------
        $clParamUpdate->UpdateData = $clRecord->getUpdateData($aFilesToSend);

        //header
        $aTabHeaderSuppl    = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte, SOAPProxy::HEADER_AutoValidate=>$autovalidate);
        $clReponseXML       = $this->m_clSOAPProxy->update($clParamUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);

        if ($autovalidate==SOAPProxy::AUTOVALIDATE_None)
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
     * @param $idColumn
     * @return ActionResult
     * @throws \Exception
     */
    public function oGetColInRecord($sIDContexte, Record $clRecord, $idColumn)
    {
        //test des valeurs des paramètres
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $this->_TestParametre(self::TP_NotEmpty, '$idColumn', $idColumn, null);

        //header
        $aTabHeaderSuppl    = array(
            SOAPProxy::HEADER_ActionContext=>$sIDContexte,
            SOAPProxy::HEADER_AutoValidate=>SOAPProxy::AUTOVALIDATE_Cancel,
            SOAPProxy::HEADER_APIUser=>0, //on utilise l'utilisateur d'application pour les droits
        );

        //paramètre de l'action liste
        $clParam     = new GetColInRecord();
        $clParam->Record = $clRecord->getIDEnreg();
        $clParam->Column = $idColumn;
        $clParam->WantContent = 1;

        $clReponseXML       = $this->m_clSOAPProxy->getColInRecord($clParam, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML, XMLResponseWS::RETURNTYPE_COLINRECORD, $idColumn );

        $data = $oRet->getData();

        //on met à jour l'enregistrement d'origine à partir de celui renvoyé par NOUTOnline
        $clRecord->updateRecordLie($data->cache);
        $clRecord->setValCol($idColumn, $data->value, false);
        $oRet->setData($clRecord);

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
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte);
        $clReponseXML = $this->m_clSOAPProxy->validate($this->_aGetTabHeader($aTabHeaderSuppl));

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
    public function oCancel($sIDContexte, $bAll=false, $bByUser=true)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte);

        $clParamCancel     = new Cancel();
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
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte);

        $oConfirmResponse = new ConfirmResponse();
        $oConfirmResponse->TypeConfirmation = $ResponseValue;

        $clReponseXML = $this->m_clSOAPProxy->ConfirmResponse($oConfirmResponse, $this->_aGetTabHeader($aTabHeaderSuppl));

        $oRet = $this->_oGetActionResultFromXMLResponse($clReponseXML);
        return $oRet;
    }

    // ------------------------------------------------------------------------------------
    // pour les Elements liés et les sous-listes

    /**
     * @param $tabParamQuery
     * @param $sIDFormulaire
     * @param $sIDContexte
     * @return ActionResult
     */
    public function oSelectElem(array $tabParamQuery, $sIDFormulaire, $sIDContexte)
	{
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);
        $aTabHeaderSuppl = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte);


//        public $ParamXML; 			// string
//        public $SpecialParamList; 	// SpecialParamListType
//        public $Checksum; 			// integer
//        public $DisplayMode; 		    // DisplayModeParamEnum
        $clParamSearch                      = new Search();
        $this->_initStructParamFromTabParamRequest($clParamSearch, $tabParamQuery);
        // Ajout des paramètres
        $clParamSearch->Table               = $sIDFormulaire;



        $clReponseXML = $this->m_clSOAPProxy->search($clParamSearch, $this->_aGetTabHeader($aTabHeaderSuppl));
        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

	/**
	 * @param $tabParamQuery
	 * @param $sIDFormulaire
	 * @param $sIDContexte
	 * @return ActionResult
	 */
    public function oCreateElem(array $tabParamQuery, $sIDFormulaire, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext=>$sIDContexte
        );

        $clParamCreate = new Create();

        // Ajout des paramètres
        $clParamCreate->Table               = $sIDFormulaire;


        $clReponseXML = $this->m_clSOAPProxy->create($clParamCreate, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
	 * @param $tabParamQuery
     * @param $sIDContexte
     * @return ActionResult
     */
    public function oModifyElem(array $tabParamQuery, $sIDContexte, $idformulaire, $idenreg)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext=>$sIDContexte
        );

        $clParamModify              = new Modify();
        $this->_initStructParamFromTabParamRequest($clParamModify, $tabParamQuery);
		$clParamModify->Table       = $idformulaire;
        $clParamModify->ParamXML    .= "<id_$idformulaire>$idenreg</id_$idformulaire>";

        $clReponseXML = $this->m_clSOAPProxy->modify($clParamModify, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

	/**
     * @param $sIDFormulaire
     * @param $sIDContexte
     * @return ActionResult
     */
    public function oSelectAmbiguous($sIDFormulaire, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDFormulaire', $sIDFormulaire, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext => $sIDContexte
        );

        // Paramètres obligatoires
        $clParamSelect             = new SelectForm();
        $clParamSelect->Form       = $sIDFormulaire;

        $clReponseXML = $this->m_clSOAPProxy->selectForm($clParamSelect, $this->_aGetTabHeader($aTabHeaderSuppl)); // Deuxième paramètre = array

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

	/**
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


    // FICHIERS

    /**
     * récupère une icone, écrit le fichier de l'icone dans le cache s'il n'existe pas déjà
     * @param $sIDIcon
     * @param $sMimeType
     * @param $sTransColor
     * @param $nWidth
     * @param $nHeight
     * @param $bSmallIcon
     * @return ActionResult
     */
    public function getIcon($sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight, $bSmallIcon=true)
    {
        $sIDColonne = $bSmallIcon ? Langage::COL_IMAGECATALOGUE_Image : Langage::COL_IMAGECATALOGUE_ImageGrande;
        return $this->getImage(Langage::TABL_ImageCatalogue, $sIDColonne, $sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight);
    }

    /**
     * récupère une icone, écrit le fichier de l'icone dans le cache s'il n'existe pas déjà
     * @param $sIDIcon
     * @param $sMimeType
     * @param $sTransColor
     * @param $nWidth
     * @param $nHeight
     * @param $sIDColonne
     * @param $sIDFormulaire
     * @return ActionResult
     */
    public function getImage($sIDFormulaire, $sIDColonne, $sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight)
    {
        //le retour c'est le chemin de fichier enregistré dans le cache
        $sFichier = $this->_getImage($sIDFormulaire, $sIDColonne, $sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sFichier);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Public);
        $clActionResult->setLastModified(new \DateTime('@'.filemtime($sFichier)));

        return $clActionResult;
    }

    /**
     * récupère un icone, écrit le fichier de l'icone dans le cache s'il n'existe pas déjà
     * @param $sIDFormulaire
     * @param $sIDEnreg
     * @param $sMimeType
     * @param $sTransColor
     * @param $nWidth
     * @param $nHeight
     * @param $sIDColonne
     * @return string
     */
    protected function _getImage($sIDFormulaire, $sIDColonne, $sIDEnreg, $sMimeType, $sTransColor, $nWidth, $nHeight)
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);

        // Création des options
        $aTabOption = array();
        if (!empty($sMimeType))
        {
            $aTabOption[RESTProxy::OPTION_MimeType] = $sMimeType;
        }

        if (!empty($sTransColor))
        {
            $aTabOption[RESTProxy::OPTION_TransColor] = $sTransColor;
        }

        if (!empty($nWidth))
        {
            $aTabOption[RESTProxy::OPTION_Width] = $nWidth;
        }

        if (!empty($nHeight))
        {
            $aTabOption[RESTProxy::OPTION_Height] = $nHeight;
        }

        //on veut le contenu
        $aTabOption[RESTProxy::OPTION_WantContent] = 1;
        //quelle image on veut ?
        $aTabOption[RESTProxy::OPTION_IDCol] = $sIDColonne;

        //on regarde si le fichier existe
        $sFile = $this->_sGetNomFichierCacheIHM($sIDFormulaire, $sIDEnreg, $aTabOption);

        if (file_exists($sFile)) //Si le fichier est déjà dans le cache
        {
            return $sFile;
        }
        else // Le fichier n'est pas dans le cache, on va le récupérer
        {
            // On essaye de récupérer l'image grande
            $oRet = $this->m_clRESTProxy->sGetColInRecord(
                $sIDFormulaire,
                $sIDEnreg,
                $aTabOption[RESTProxy::OPTION_IDCol],
                array(),
                $aTabOption,
                $clIdentification,
                $sFile
            );

            return $oRet->content;
        }
    }

    /**
     * @param $idformulaire
     * @param $query
     * @param $idcontext
     * @param $idcallingcolumn
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
     * @param $idformulaire
     * @param $query
     * @param $idcontext
     * @param $idcallingcolumn
     */
    private function _getSuggest($idcontext, $idformulaire, $idcallingcolumn, $query)
    {
        // Création des options
        $aTabOption = array();
        $aTabParam  = array('CallingColumn'=>$idcallingcolumn);

        $clIdentification = $this->_clGetIdentificationREST($idcontext, true);

        $sRet = $this->m_clRESTProxy->sGetSuggestFromQuery(
            $idformulaire,
            $query,
            $aTabParam,
            $aTabOption,
            $clIdentification
        );

        return $sRet;
    }

    // Langage::TABL_ModeleFichier

	/**
	 * récupère un fichier
	 * @param $idForm
	 * @param $idColumn
	 * @param $idRecord
	 * @return ActionResult
	 */
	public function getFile($idForm, $idColumn, $idRecord)
	{
		$sFichier = $this->_getFile($idForm, $idColumn, $idRecord);

		$clActionResult = new ActionResult(null);
		$clActionResult->setData($sFichier);

		//gestion du cache
		$clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Public);
		// $clActionResult->setLastModified(new \DateTime('@'.filemtime($sFichier)));

		return $clActionResult;
	}


    /**
     * récupère un fichier pour téléchargement
     * @param $idForm
     * @param $idColumn
     * @param $idRecord
     * @return File
     */
    private function _getFile($idForm, $idColumn, $idRecord)
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);
        $sFile = null; // Pour stocker le contenu du fichier

        // --------------------------------------------
        // VERSION REST - Plus rapide
        $aTabOption = array();
        //on veut le contenu
        $aTabOption[RESTProxy::OPTION_WantContent] = 1;

        // $sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification, $sDest = ''
        $clFileResponseRest =  $this->m_clRESTProxy->sGetColInRecord(
            $idForm,    		// ID Tableau
            $idRecord,          // ID Enregistrement
            $idColumn,          // ID Colonne
            array(),
            $aTabOption,
            $clIdentification,
            $sFile
        );

        // --------------------------------------------
        // VERSION SOAP
		/*
        $clParamGetColInRecord              = new GetColInRecord();
        $clParamGetColInRecord->WantContent = 1;      // On veut le contenu du fichier
        $clParamGetColInRecord->Column      = $idColumn;   // Ajout des infos obligatoires sur le fichier
        $clParamGetColInRecord->Record      = $idRecord;   // Ajout des infos obligatoires sur le fichier

        $aTabHeaderSuppl = array();

        $clFileResponseSoap = $this->m_clSOAPProxy->getColInRecord($clParamGetColInRecord, $this->_aGetTabHeader($aTabHeaderSuppl));
		*/
        // --------------------------------------------


        return $clFileResponseRest;
    }

    /**
     * @param $idForm
     * @param $idColumn
     * @param $idRecord
     * @param $height
     * @return ActionResult
     */
    public function getImagePreview($idForm, $idColumn, $idRecord, $height)
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);
        $sFile = null; // Pour stocker le contenu du fichier

        // --------------------------------------------
        // VERSION REST - Plus rapide
        $aTabOption = array();
        //on veut le contenu
        $aTabOption[RESTProxy::OPTION_WantContent]  = 1;
        $aTabOption[RESTProxy::OPTION_Height]       = $height;

        // $sIDTableau, $sIDEnreg, $sIDColonne, $aTabParam, $aTabOption, Identification $clIdentification, $sDest = ''
        $clFileResponseRest =  $this->m_clRESTProxy->sGetColInRecord(
            $idForm,    		// ID Tableau
            $idRecord,          // ID Enregistrement
            $idColumn,          // ID Colonne
            array(),
            $aTabOption,
            $clIdentification,
            $sFile
        );

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($clFileResponseRest);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Public);
        // $clActionResult->setLastModified(new \DateTime('@'.filemtime($sFichier)));

        return $clActionResult;
    }


    /**
     * écrit un fichier dans le cache
     * @param $file
     * @param $fileID
     */
    public function cacheFile($file, $fileID)
    {
        $filePath = $this->_cacheFile($file, $fileID); // Générer le chemin du fichier dans le cache
        @copy($file, $filePath); // Copie du fichier dans le cache

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($filePath);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Public);
        // $clActionResult->setLastModified(new \DateTime('@'.filemtime($filePath))); // Erreur si le fichier n'existe pas

        return $clActionResult;
    }

    /**
     * le fichier doit être déplacé dans le cache
     * @param $file
     * @param $fileID
     */
    private function _cacheFile($file, $fileID)
    {
        $clIdentification = $this->_clGetIdentificationREST('', true);

        // Création des options
        $aTabOption = array();
        $sMimeType = $file->getClientMimeType();

        if (!empty($sMimeType))
        {
            $aTabOption[RESTProxy::OPTION_MimeType] = $sMimeType;
        }

        $aTabOption[RESTProxy::OPTION_WantContent] = 1; // Dans le doute

        // ($sIDTab, $sIDElement, $aTabOption)
        $sFilePath = $this->_sGetCacheFilePath($fileID, $aTabOption);
        $sFilePath = str_replace('\\','/',$sFilePath); // Sanityze path


        if (file_exists($sFilePath)) // Vérifie si le fichier est dans le cache ?
        {
            $fileExists = true;
            // Soit le fichier existe et on le remplace
            // Soit le fichier n'existe pas et on le met en cache
        }

        return $sFilePath;
    }



    /**
     * on construit la structure qui contient tous les fichiers à envoyer
     * @param $clRecord
     * @return array
     */
    protected function _getModifiedFiles(Record $clRecord)
    {
        $aModifiedFiles = array();

        $structElem     = $clRecord->clGetStructElem();
        $fiche          = $structElem->getFiche();

        $aModifiedFiles = $this->_getFilesFromSection($clRecord, $fiche, $aModifiedFiles);


        return $aModifiedFiles;
    }

    /**
     * recherche récursive des fichiers
     * @param $clRecord
     * @param $section
     * @param $aModifiedFiles
     * @return array
     */
    protected function _getFilesFromSection(Record $clRecord, StructureSection $section, $aModifiedFiles)
    {
        $structColonne  = $section->getTabStructureColonne();

        // Contient des structuresDonnes
        foreach($structColonne as $key=>$colonne)
        {
            /**@var StructureDonnee $colonne*/
            $idColonne      = $colonne->getIDColonne();
            $typeElement    = $colonne->getTypeElement();

            if($typeElement == StructureColonne::TM_Separateur)
            {
                /**@var StructureSection $colonne*/
                $aModifiedFiles = $this->_getFilesFromSection($clRecord, $colonne, $aModifiedFiles);
            }
            else if($typeElement == StructureColonne::TM_Fichier && $clRecord->isModified($idColonne))
            {
                // On a un fichier modifié, on doit le récupérer
                $file       = new \stdClass();
                $file->path = "";

                $fullPath = $clRecord->getValCol($idColonne);

				if($fullPath != "")
				{
					$stringElements = explode('?', $fullPath); // Le nom du fichier se trouve après le path

					$file->path 	= $stringElements[0];
					$file->fileName = $stringElements[1];
					$file->cacheID 	= basename($file->path);
					$file->content 	= file_get_contents($file->path);
					$file->mimeType = mime_content_type($file->path);
					$file->size 	= filesize($file->path);
				}

                // Ajout du fichier dans le tableau
                $aModifiedFiles[$idColonne] = $file;
            }
        }

        return $aModifiedFiles;
    }

    // Fin Fichiers
    // ------------------------------------------------------------------------------------


	const REPCACHE      	= 'NOUTClient';
	const REPCACHE_IHM  	= 'ihm';
	const REPCACHE_UPLOAD	= 'upload';

	const TP_NotEmpty   	= 1;
	const TP_InArray    	= 2;
    const MaxEnregs     	= 200;	// Nombre maximum d'éléments sur une page


    const PARAM_SPECIALPARAMLIST  = 'SpecialParamList';
    const PARAM_PARAMXML          = 'ParamXML';
    const PARAM_CALLINGCOLUMN     = 'CallingColumn';
    const PARAM_DISPLAYMODE       = 'DisplayMode';
    const PARAM_CHECKSUM          = 'Checksum';
    const PARAM_CALLINGINFO       = 'CallingInfo';
}
