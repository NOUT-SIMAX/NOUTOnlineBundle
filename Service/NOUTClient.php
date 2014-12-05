<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 11:25
 */

namespace NOUT\Bundle\ContextesBundle\Service;


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
use NOUT\Bundle\NOUTOnlineBundle\Entity\REST\Identification;
use NOUT\Bundle\NOUTOnlineBundle\Service\OnlineServiceFactory;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy as SOAPProxy;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use Symfony\Component\Security\Core\SecurityContext;

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
	 * @var \Symfony\Component\Security\Core\SecurityContext
	 */
	private $__security;

	/**
	 * @param Router $router
	 * @param SecurityContext $security
	 * @param OnlineServiceFactory $serviceFactory
	 * @param ConfigurationDialogue $configurationDialogue
	 */
	public function __construct(SecurityContext $security, OnlineServiceFactory $serviceFactory, ConfigurationDialogue $configurationDialogue)
	{
		$this->__security=$security;
		$this->m_clSOAPProxy = $serviceFactory->clGetSOAPProxy($configurationDialogue);
		$this->m_clRESTProxy = $serviceFactory->clGetRESTProxy($configurationDialogue);

		$this->m_clConfigurationDialogue=$configurationDialogue;
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

		$sRet = $this->m_clRESTProxy->sGetColInRecord(Langage::TABL_ImageCatalogue, $sIDIcon, Langage::COL_IMAGECATALOGUE_ImageGrande, array(), $aTabOption, $clIdentification);
		if (!empty($sRet))
			return $sRet;

		return $this->m_clRESTProxy->sGetColInRecord(Langage::TABL_ImageCatalogue, $sIDIcon, Langage::COL_IMAGECATALOGUE_Image, array(), $aTabOption, $clIdentification);
	}
} 