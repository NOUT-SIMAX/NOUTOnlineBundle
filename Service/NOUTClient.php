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
use NOUT\Bundle\ContextsBundle\Entity\Menu\MenuLoader;
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

use NOUT\Bundle\SessionManagerBundle\Security\Authentication\Provider\NOUTToken;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Security\Core\SecurityContext;

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
	 * @var \Symfony\Component\Security\Core\SecurityContext
	 */
	private $__security;

	/**
	 * @var NOUTCache
	 */
	private $m_clCacheSession;

	/**
	 * @param SecurityContext       $security
	 * @param OnlineServiceFactory  $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 * @param                       $sCacheDir
	 * @throws \Exception
	 */
	public function __construct(SecurityContext $security, OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue, $sCacheDir)
	{
		$this->__security = $security;

		$this->m_sCacheDir   = $sCacheDir.'/'.self::REPCACHE;


		$oSecurityToken = $this->_oGetToken();

		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
		$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

		$this->m_clConfigurationDialogue = $configurationDialogue;

		//création du cache pour la session
        if ($oSecurityToken instanceof NOUTToken)
        {
            $sSessionToken = $oSecurityToken->getSessionToken();
            $this->m_clCacheSession = new NOUTCache($sCacheDir.'/'.self::REPCACHE, $sSessionToken);
        }
	}

	/**
	 * @return NOUTToken
	 */
	protected function _oGetToken()
	{
		return $this->__security->getToken();
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
		$oUser  = $oToken->getUser();

		return new ConnectionInfos($oUser->getUsername());
	}


    /**
     * @param $oUser
     * @return UsernameToken
     */
    protected function _oGetUsernameToken($oUser)
    {
        $oUsernameToken = new UsernameToken(
            $oUser->getUsername(),
            $oUser->getPassword(),
            $this->m_clConfigurationDialogue->getModeAuth(),
            $this->m_clConfigurationDialogue->getSecret()
        );

        return $oUsernameToken;
    }

    /**
     * @param $oUser
     * @return array|UsernameToken
     */
    protected function _oGetUsernameTokenSOAP($oUser)
    {
        $oUsernameToken = $this->_oGetUsernameToken($oUser);
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
        $oUser  =  $oToken->getUser();

		$clIdentification->m_clUsernameToken   = $this->_oGetUsernameToken($oUser);
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
		$oUser  = $oToken->getUser();

		$aTabHeader = array(
			SOAPProxy::HEADER_UsernameToken  => $this->_oGetUsernameTokenSOAP($oUser),
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
		$clFileNPI->EmpileCondition(Langage::COL_OPTIONMENUPOURTOUS_IDAction, ConditionColonne::COND_WITHRIGHT, 1);
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
	 * retourne un tableau d'option de menu
	 * @return ActionResult
	 */
	public function getTabMenu()
	{
		$clReponseXML_OptionMenu = $this->_oGetTabMenu_OptionMenu();
		$clReponseXML_Menu       = $this->_oGetTabMenu_Menu();

		$clActionResult = new ActionResult(null);
		$clActionResult->setData(MenuLoader::s_aGetTabMenu($clReponseXML_OptionMenu, $clReponseXML_Menu));

		//le menu dépend de l'utilisateur, c'est un cache privé
		$clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Private);

		return $clActionResult;
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
    protected function _sGetRepCache()
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
        $sRep = $this->_sGetRepCache();

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
	 * Execute une action via la phrase OU par son ID
	 *
	 * @param string $sPhrase
     * @param string $sIDAction
	 * @param string $sIDContexte
	 * @param string $sTabParam
	 * @param string $sIDCallingColumn
	 * @param SpecialParamListType $oParamListe
	 * @param string $sDisplayMode
	 * @param string $sChecksum
	 * @return ActionResult
	 */
	public function oExecAction($sPhrase, $sIDAction, $sIDContexte = '', $sTabParam = '' , $sIDCallingColumn = '', SpecialParamListType $oParamListe = null, $sDisplayMode = SOAPProxy::DISPLAYMODE_Liste, $sChecksum = '')
	{
        //--------------------------------------------------------------------------------------------
        // Création de $clParamExecute
		$clParamExecute = new Execute();

        if (!empty($sIDAction))
        {
            $clParamExecute->ID           = $sIDAction;             // identifiant de l'action (String)
        }

        if (!empty($sPhrase))
        {
            $clParamExecute->Sentence     = $sPhrase;               // phrase de l'action (Sting)
        }

        //if (!is_null($sTabParam))
		if($sTabParam != '')
        {
            // $aTabParam est à transformer en string
			// $clParamExecute->ParamXML     = $this->_sParamArray2ParamString($aTabParam);

			// Transformation déjà faire dans s_GetTabParam de Request2APIParam.php
			$clParamExecute->ParamXML     = $sTabParam;
        }

		$clParamExecute->SpecialParamList = $oParamListe;           // paramètre supplémentaire pour les listes (SpecialParamListType)
		$clParamExecute->Checksum         = $sChecksum;             // checksum pour utilisation du cache (Integer)
		$clParamExecute->CallingColumn    = $sIDCallingColumn;      // identifiant de la colonne d'appel (String)
		$clParamExecute->DisplayMode      = SOAPProxy::s_sVerifDisplayMode($sDisplayMode, SOAPProxy::DISPLAYMODE_Liste); // (DisplayModeParamEnum)

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
	 * Execute une action via son id
	 * @param string $sIDAction
	 * @param string $sIDContexte
	 * @param string $sTabParam
	 * @param string $sIDCallingColumn
	 * @param SpecialParamListType $oParamListe
	 * @param string $sDisplayMode
	 * @param string $sChecksum
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecIDAction($sIDAction, $sIDContexte = '', $sTabParam = '', $sIDCallingColumn = '', SpecialParamListType $oParamListe = null, $sDisplayMode = SOAPProxy::DISPLAYMODE_Liste, $sChecksum = '')
	{
        // On recopie les paramètres
        return $this->oExecAction(null, $sIDAction, $sIDContexte, $sTabParam, $sIDCallingColumn, $oParamListe, $sDisplayMode, $sChecksum);
	}



    /**
     * Execute une action via sa phrase
     * @param string $sPhrase
     * @param string $sIDContexte
     * @param string $aTabParam
     * @param string $sIDCallingColumn
     * @param SpecialParamListType $oParamListe
     * @param string $sDisplayMode
     * @param string $sChecksum
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
     */
    public function oExecPhraseAction($sPhrase, $sIDContexte = '', $sTabParam = '', $sIDCallingColumn = '', SpecialParamListType $oParamListe = null, $sDisplayMode = SOAPProxy::DISPLAYMODE_Liste, $sChecksum = '')
    {
        // On recopie les paramètres
        return $this->oExecAction($sPhrase, null, $sIDContexte, $sTabParam, $sIDCallingColumn, $oParamListe, $sDisplayMode, $sChecksum);
    }



	/**
	 * Affichage d'une liste
	 * @param $sIDAction
	 * @param string $sIDContexte
	 * @param array $aTabParam
	 * @param string $sIDCallingColumn
	 * @param SpecialParamListType $oParamListe
	 * @param string $sDisplayMode
	 * @param string $sChecksum
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecList($sIDTableau, $sIDContexte = '', $aTabParam = array(), $sIDCallingColumn = '', SpecialParamListType $oParamListe = null, $sDisplayMode = SOAPProxy::DISPLAYMODE_Liste, $sChecksum = '')
	{
		//paramètre de l'action liste
		$clParamListe     = new ListParams();
        $clParamListe->Table = $sIDTableau;

		//$clParamExecute->Sentence								// phrase de l'action
        $clParamListe->SpecialParamList = $oParamListe;      	//paramètre supplémentaire pour les listes
        $clParamListe->Checksum         = $sChecksum;        	// checksum pour utilisation du cache
        $clParamListe->CallingColumn    = $sIDCallingColumn; 	// identifiant de la colonne d'appel
        $clParamListe->DisplayMode      = SOAPProxy::s_sVerifDisplayMode($sDisplayMode, SOAPProxy::DISPLAYMODE_Liste);       // DisplayModeParamEnum
        // $clParamListe->ParamXML         = $aTabParam;               // paramètre de l'action -  valeurs des filtres

		//header
		$aTabHeaderSuppl = array();
		if (!empty($sIDContexte))
		{
			$aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext] = $sIDContexte;
		}

        $start = 0;
        $count = 20;

        // à faire de la forme ?first=___&size=___
        // Dans webix : size // group //page
        $clParamListe->SpecialParamList->First = $start;
        $clParamListe->SpecialParamList->Length = $count;

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
	 * @return ActionResult
	 * @throws \Exception
	 */
	protected function _oGetActionResultFromXMLResponse(XMLResponseWS $clReponseXML)
	{
		$clActionResult = new ActionResult($clReponseXML);

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


        // -----------------------------------------------------
        // Fichiers

        // Chercher tous les fichiers modifiés dans le Record // similaire à getStructforUpdateSOAP => getColonneFileModified
		$aFilesToSend = $this->_getModifiedFiles($clRecord);

        // -----------------------------------------------------

		$paramUpdate = $clRecord->getStructForUpdateSOAP($aFilesToSend);

		//header
		$aTabHeaderSuppl    = array(SOAPProxy::HEADER_ActionContext=>$sIDContexte, SOAPProxy::HEADER_AutoValidate=>$autovalidate);
		$clReponseXML       = $this->m_clSOAPProxy->update($paramUpdate, $this->_aGetTabHeader($aTabHeaderSuppl));

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


//        public $Table; 				// string
//        public $ParamXML; 			// string
//        public $SpecialParamList; 	// SpecialParamListType
//        public $CallingColumn; 		// string ! Todo
//        public $Checksum; 			// integer
//        public $DisplayMode; 		    // DisplayModeParamEnum
        $clParamSearch                      = new Search();

        // Ajout des paramètres
        $clParamSearch->Table               = $sIDFormulaire;

//        public $First; 				// integer
//        public $Length; 			    // integer
//        public $WithBreakRow; 		// integer
//        public $WithEndCalculation;	// integer
//        public $ChangePage; 		    // integer
//        public $Sort1; 				// SortType
//        public $Sort2; 				// SortType
//        public $Sort3; 				// SortType
        $clParamSearch->SpecialParamList                = new SpecialParamListType();
        $clParamSearch->SpecialParamList->First         = $tabParamQuery['SpecialParamList']->First;
        $clParamSearch->SpecialParamList->Length        = $tabParamQuery['SpecialParamList']->Length;
        $clParamSearch->SpecialParamList->ChangePage    = 0; //$tabParamQuery['SpecialParamList']->ChangePage;


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
        $clParamCreate->SpecialParamList    = new SpecialParamListType();	// Pas besoin de ces paramètres ?


        $clReponseXML = $this->m_clSOAPProxy->create($clParamCreate, $this->_aGetTabHeader($aTabHeaderSuppl));

        return $this->_oGetActionResultFromXMLResponse($clReponseXML);
    }

    /**
	 * @param $tabParamQuery
     * @param $sIDContexte
     * @return ActionResult
     */
    public function oModifyElem(array $tabParamQuery, $sIDContexte)
    {
        $this->_TestParametre(self::TP_NotEmpty, '$sIDContexte', $sIDContexte, null);

        $aTabHeaderSuppl = array(
            SOAPProxy::HEADER_ActionContext=>$sIDContexte
        );

        $clParamModify              = new Modify();
		$clParamModify->Table       = $tabParamQuery['Table'];
		$clParamModify->ParamXML    = $tabParamQuery['ParamXML'];

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
        $usernameToken  = $this->_oGetUsernameToken($token->getUser());
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
     * @return ActionResult
     */
    public function getIcon($sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight)
    {
		//le retour c'est le chemin de fichier enregistré dans le cache
        $sFichier = $this->_getIcon($sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight);

        $clActionResult = new ActionResult(null);
        $clActionResult->setData($sFichier);

        //gestion du cache
        $clActionResult->setTypeCache(ActionResultCache::TYPECACHE_Public);
        $clActionResult->setLastModified(new \DateTime('@'.filemtime($sFichier)));

        return $clActionResult;
    }


    /**
     * récupère un icone, écrit le fichier de l'icone dans le cache s'il n'existe pas déjà
     * @param $sIDIcon
     * @param $sMimeType
     * @param $sTransColor
     * @param $nWidth
     * @param $nHeight
     * @return string
     */
    public function _getIcon($sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight)
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

        //on regarde si le fichier existe
        $sFile = $this->_sGetNomFichierCacheIHM(Langage::TABL_ImageCatalogue, $sIDIcon, $aTabOption);

        if (file_exists($sFile)) //Si le fichier est déjà dans le cache
        {
            return $sFile;
        }
        else // Le fichier n'est pas dans le cache, on va le récupérer
        {
            // On essaye de récupérer l'image grande
            $sRet = $this->m_clRESTProxy->sGetColInRecord(
                Langage::TABL_ImageCatalogue,
                $sIDIcon,
                Langage::COL_IMAGECATALOGUE_ImageGrande,
                array(),
                $aTabOption,
                $clIdentification,
                $sFile
            );

            if (!empty($sRet->content))
            {
                return $sRet;
            }

            // Sinon on donne l'image de taille normale
            return $this->m_clRESTProxy->sGetColInRecord(
                Langage::TABL_ImageCatalogue,
                $sIDIcon,
                Langage::COL_IMAGECATALOGUE_Image,
                array(),
                $aTabOption,
                $clIdentification,
                $sFile
            );
        }

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
     * écrit un fichier dans le cache
     * @param UploadedFile $file
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
     * @param UploadedFile $file
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
}
