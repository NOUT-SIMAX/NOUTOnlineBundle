<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\ContextesBundle\Service;


use NOUT\Bundle\ContextesBundle\Entity\ActionResult;
use NOUT\Bundle\ContextesBundle\Entity\ConnectionInfos;
use NOUT\Bundle\ContextesBundle\Entity\Menu\MenuLoader;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionOperateur;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\RequestColList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SpecialParamListType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Security\Core\SecurityContext;

class NOUTClient
{
	const REPCACHE      = 'NOUTClient';
	const REPCACHE_IHM  = 'ihm';
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
	 * @param Router $router
	 * @param SecurityContext $security
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 */
	public function __construct(SecurityContext $security, OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue, $sCacheDir)
	{
		$this->__security=$security;

		$this->m_sCacheDir=$sCacheDir.'/'.self::REPCACHE;
		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
		$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

		$this->m_clConfigurationDialogue=$configurationDialogue;

		//création du repertoire de
	}


	protected function _clGetOptionDialogue()
	{
		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->DisplayValue = OptionDialogue::DISPLAY_No_ID;
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
		$oToken = $this->__security->getToken();
		$oUser = $oToken->getUser();

		return new ConnectionInfos($oUser->getUsername());
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
		$oToken = $this->__security->getToken();
		$oUser =  $oToken->getUser();

		$clIdentification->m_clUsernameToken = new UsernameToken($oUser->getUsername(), $oUser->getPassword());
		$clIdentification->m_sTokenSession = $oToken->getSessionToken();
		$clIdentification->m_sIDContexteAction = $sIDContexteAction;
		$clIdentification->m_bAPIUser = $bAPIUser;

		return $clIdentification;
	}


	/**
	 * @param array $aHeaderSup
	 * @return array
	 */
	protected function _aGetTabHeader(array $aHeaderSup=null)
	{
		// récupération de l'utilsateur connecté
		$oToken = $this->__security->getToken();
		$oUser =  $oToken->getUser();

		$aTabHeader=array(
			SOAPProxy::HEADER_UsernameToken=>new UsernameToken($oUser->getUsername(), $oUser->getPassword()),
			SOAPProxy::HEADER_SessionToken=>$oToken->getSessionToken(),
			SOAPProxy::HEADER_OptionDialogue=>$this->_clGetOptionDialogue(),
		);


		if (!empty($aHeaderSup))
			$aTabHeader=array_merge($aTabHeader, $aHeaderSup);

		return $aTabHeader;
	}

	/**
	 * @param $sIDform identifiant du formulaire
	 * @param ConditionFileNPI $clFileNPI condition pour la requete
	 * @param array $TabColonneAff tableau des colonnes a afficher
	 * @param array $TabHeaderSuppl tableau des headers
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	protected function _oRequest($sIDform, ConditionFileNPI $clFileNPI, array $TabColonneAff, array $TabHeaderSuppl)
	{
		$clParamRequest = new Request();
		$clParamRequest->Table = $sIDform;
		$clParamRequest->CondList = $clFileNPI->sToSoap();
		$clParamRequest->ColList =new ColListType($TabColonneAff);

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
			SOAPProxy::HEADER_AutoValidate=>SOAPProxy::AUTOVALIDATE_Cancel,  //on ne garde pas le contexte ouvert
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
			SOAPProxy::HEADER_AutoValidate=>SOAPProxy::AUTOVALIDATE_Cancel, //on ne garde pas le contexte ouvert
			SOAPProxy::HEADER_APIUser=>SOAPProxy::APIUSER_Active,           //on force l'utilisation de l'user d'application (max) car un utilisateur classique n'aura pas les droit d'executer cette requete
		);

		return $this->_oRequest(Langage::TABL_MenuPourTous, new ConditionFileNPI(), $aTabColonne, $aTabHeaderSuppl);
	}

	/**
	 * retourne un tableau d'option de menu
	 * @return array
	 */
	public function getTabMenu()
	{
		$clReponseXML_OptionMenu = $this->_oGetTabMenu_OptionMenu();
		$clReponseXML_Menu = $this->_oGetTabMenu_Menu();

		return MenuLoader::s_aGetTabMenu($clReponseXML_OptionMenu, $clReponseXML_Menu);
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
	public function getIcon($sIDIcon, $sMimeType, $sTransColor, $nWidth, $nHeight)
	{
		$clIdentification = $this->_clGetIdentificationREST('', true);

		$aTabOption = array();
		if (!empty($sMimeType))
			$aTabOption[RESTProxy::OPTION_MimeType]=$sMimeType;

		if (!empty($sTransColor))
			$aTabOption[RESTProxy::OPTION_TransColor]=$sTransColor;

		if (!empty($nWidth))
			$aTabOption[RESTProxy::OPTION_Width]=$nWidth;

		if (!empty($nHeight))
			$aTabOption[RESTProxy::OPTION_Height]=$nHeight;

		//on veut le contenu
		$aTabOption[RESTProxy::OPTION_WantContent]=1;

		//on regarde si le fichier existe
		$sFile = $this->_sGetNomFichierCacheIHM(Langage::TABL_ImageCatalogue, $sIDIcon, $aTabOption);

		if (file_exists($sFile))
			return $sFile;

		$sRet = $this->m_clRESTProxy->sGetColInRecord(Langage::TABL_ImageCatalogue, $sIDIcon, Langage::COL_IMAGECATALOGUE_ImageGrande, array(), $aTabOption, $clIdentification, $sFile);
		if (!empty($sRet))
			return $sRet;

		return $this->m_clRESTProxy->sGetColInRecord(Langage::TABL_ImageCatalogue, $sIDIcon, Langage::COL_IMAGECATALOGUE_Image, array(), $aTabOption, $clIdentification, $sFile);
	}

	/**
	 * @param $sIDTab
	 * @return string
	 */
	protected function _sGetRepCacheIHM($sIDTab)
	{
		$oToken = $this->__security->getToken();
		$clLangage = $oToken->getLangage();

		$sRep = $this->m_sCacheDir.'/'.self::REPCACHE_IHM.'/'.$clLangage->getVersionLangage();

		switch($sIDTab)
		{
		case Langage::TABL_ImageCatalogue:
			$sRep.=	'/'.$clLangage->getVersionIcone();
			break;
		}

		if (!file_exists($sRep))
			mkdir($sRep, 0777, true);

		return $sRep;
	}

	/**
	 * retourne le nom de fichier pour le cache
	 * @param $sID
	 * @param $aTabOption array
	 */
	protected function _sGetNomFichierCacheIHM($sIDTab, $sIDElement, $aTabOption)
	{
		$sRep = $this->_sGetRepCacheIHM($sIDTab);

		//on tri le tableau pour toujours l'avoir dans le même ordre
		ksort($aTabOption);

		$sListeOption='';
		foreach($aTabOption as $sOption=>$valeur)
		{
			if (!empty($sListeOption))
				$sListeOption.='_';

			$sListeOption.=$valeur;
		}

		return $sRep.'/'.$this->_sSanitizeFilename($sIDElement.'_'.$sListeOption);
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
			'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
			'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
			'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
			'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
			'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
			'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
			'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f'
		);
		$filename = strtr($filename, $replace_chars);
		// convert & to "and", @ to "at", and # to "number"
		$filename = preg_replace(array('/[\&]/', '/[\@]/', '/[\#]/'), array('-and-', '-at-', '-number-'), $filename);
		$filename = preg_replace('/[^(\x20-\x7F)]*/','', $filename); // removes any special chars we missed
		$filename = str_replace(' ', '-', $filename); // convert space to hyphen
		$filename = str_replace('/', '-', $filename); // convert / to hyphen
		$filename = str_replace('\\', '-', $filename); // convert \ to hyphen
		$filename = str_replace('\'', '', $filename); // removes apostrophes
		$filename = preg_replace('/[^\w\-\.]+/', '', $filename); // remove non-word chars (leaving hyphens and periods)
		$filename = preg_replace('/[\-]+/', '-', $filename); // converts groups of hyphens into one
		return strtolower($filename);
	}

	/**
	 * Execute une action via son id
	 * @param $sIDAction
	 * @param string $sIDContexte
	 * @param array $aTabParam
	 * @param string $sIDCallingColumn
	 * @param SpecialParamListType $oParamListe
	 * @param string $sDisplayMode
	 * @param string $sChecksum
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecIDAction($sIDAction, $sIDContexte='', $aTabParam=array(), $sIDCallingColumn='', SpecialParamListType $oParamListe=null, $sDisplayMode=SOAPProxy::DISPLAYMODE_Liste, $sChecksum='')
	{
		//paramètre de l'action
		$clParamExecute = new Execute();
		$clParamExecute->ID = $sIDAction;                   // identifiant de l'action
		//$clParamExecute->Sentence                         // phrase de l'action
		$clParamExecute->SpecialParamList = $oParamListe;   //paramètre supplémentaire pour les listes
		$clParamExecute->Checksum = $sChecksum;             // checksum pour utilisation du cache
		$clParamExecute->CallingColumn = $sIDCallingColumn; // identifiant de la colonne d'appel
		$clParamExecute->DisplayMode = SOAPProxy::s_sVerifDisplayMode($sDisplayMode, SOAPProxy::DISPLAYMODE_Liste);       // DisplayModeParamEnum
		//$clParamExecute->ParamXML = $aTabParam;             // paramètre de l'action

		//header
		$aTabHeaderSuppl = array();
		if (!empty($sIDContexte))
			$aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext]=$sIDContexte;

		return $this->_oExecute($clParamExecute, $aTabHeaderSuppl);
	}

	/**
	 * Execute une action via la phrase
	 *
	 * @param $sPhrase
	 * @param string $sIDContexte
	 * @param array $aTabParam
	 * @param string $sIDCallingColumn
	 * @param SpecialParamListType $oParamListe
	 * @param string $sDisplayMode
	 * @param string $sChecksum
	 * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS
	 */
	public function oExecSentence($sPhrase, $sIDContexte='', array $aTabParam=array(), $sIDCallingColumn='', SpecialParamListType $oParamListe=null, $sDisplayMode=SOAPProxy::DISPLAYMODE_Liste, $sChecksum='')
	{
		//paramètre de l'action
		$clParamExecute = new Execute();
		//$clParamExecute->ID = $sIDAction;                 // identifiant de l'action
		$clParamExecute->Sentence = $sPhrase;               // phrase de l'action
		$clParamExecute->SpecialParamList = $oParamListe;   //paramètre supplémentaire pour les listes
		$clParamExecute->Checksum = $sChecksum;             // checksum pour utilisation du cache
		$clParamExecute->CallingColumn = $sIDCallingColumn; // identifiant de la colonne d'appel
		$clParamExecute->DisplayMode = SOAPProxy::s_sVerifDisplayMode($sDisplayMode, SOAPProxy::DISPLAYMODE_Liste);       // DisplayModeParamEnum
		//$clParamExecute->ParamXML = $aTabParam;             // paramètre de l'action

		//header
		$aTabHeaderSuppl = array();
		if (!empty($sIDContexte))
			$aTabHeaderSuppl[SOAPProxy::HEADER_ActionContext]=$sIDContexte;

		return $this->_oExecute($clParamExecute, $aTabHeaderSuppl);
	}

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
		$clActionResult = new ActionResult($clReponseXML->sGetReturnType());

		switch($clActionResult->ReturnType)
		{
			case XMLResponseWS::RETURNTYPE_EMPTY:
			case XMLResponseWS::RETURNTYPE_REPORT:
			case XMLResponseWS::RETURNTYPE_VALUE:
			case XMLResponseWS::RETURNTYPE_REQUESTFILTER:
			case XMLResponseWS::RETURNTYPE_CHART:
			case XMLResponseWS::RETURNTYPE_NUMBEROFCHART:

			case XMLResponseWS::RETURNTYPE_LIST:

			case XMLResponseWS::RETURNTYPE_XSD:
			case XMLResponseWS::RETURNTYPE_IDENTIFICATION:
			case XMLResponseWS::RETURNTYPE_PLANNING:
			case XMLResponseWS::RETURNTYPE_GLOBALSEARCH:
			case XMLResponseWS::RETURNTYPE_LISTCALCULATION:
			case XMLResponseWS::RETURNTYPE_EXCEPTION:

			case XMLResponseWS::RETURNTYPE_AMBIGUOUSACTION:
			case XMLResponseWS::RETURNTYPE_MESSAGEBOX:
			case XMLResponseWS::RETURNTYPE_VALIDATEACTION:
			case XMLResponseWS::RETURNTYPE_VALIDATERECORD:
			case XMLResponseWS::RETURNTYPE_PRINTTEMPLATE:

			case XMLResponseWS::RETURNTYPE_MAILSERVICERECORD:
			case XMLResponseWS::RETURNTYPE_MAILSERVICELIST:
			case XMLResponseWS::RETURNTYPE_MAILSERVICESTATUS:
			case XMLResponseWS::RETURNTYPE_WITHAUTOMATICRESPONSE:
			{
				throw new \Exception("Type de retour $clActionResult->ReturnType non géré", 1);
			}

			case XMLResponseWS::RETURNTYPE_RECORD:
			{
				$clParser = new ReponseWSParser();
				$clParser->InitFromXmlXsd($clReponseXML);

				$clActionResult->setData($clParser->clGetRecord($clReponseXML->clGetForm(), $clReponseXML->clGetElement()));
				break;
			}
		}

		return $clActionResult;
	}
}