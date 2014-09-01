<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\MessageBox;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExtranetUserType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetCalculation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;

/**
 * Class NOUTOnlineTest
 * classe pour tester NOUTOnline en mode Intranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 *
 * phpunip -c app --filter testCreate_OK
 */
class NOUTOnlineTest extends \PHPUnit_Framework_TestCase
{
	protected $m_clConfig;
	protected $m_clNOUTOnline;

	public function __construct()
	{
		//on instancie la configuration de NOUTOnline
		$sService = 'http://127.0.0.1:8062';
		//on récupére le prefixe (http | https);
		$sProtocolPrefix = substr($sService,0,strpos($sService,'//')+2 );
		list($sHost,$sPort) = explode(':', str_replace($sProtocolPrefix,'',$sService) );

		//il faut récupérer la wsdl depuis le service
		//adresse de la wsdl :  /getwsdl?
		$sHttpWSDL = $sService.'/GetWSDL?';
		$sWSDL = file_get_contents($sHttpWSDL);

		$sEndPoint = './Service.wsdl';
		file_put_contents($sEndPoint, $sWSDL);

		$this->m_clConfig = new ConfigurationDialogue($sEndPoint, true, $sHost, $sPort,$sProtocolPrefix);

		//le logger
		$clLogger = new NOUTOnlineLogger(null, false);

		//ici on instancie NOUTOnline
		$this->m_clNOUTOnline = new OnlineServiceProxy($this->m_clConfig, $clLogger);
	}

	/**
	 * renvoi le username token avec les bonnes infomations pour la connexion
	 * @return UserNameToken
	 */
	protected function _clGetUsernameToken()
	{
		return new UsernameToken('superviseur', '');
	}

	/**
	 * @return OptionDialogue
	 */
	protected function _clGetOptionDialogue()
	{
		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->DisplayValue = OnlineServiceProxy::FORMHEAD_UNDECODED_SPECIAL_ELEM;;
		$clOptionDialogue->Readable = 0;
		$clOptionDialogue->EncodingOutput = 0;
		$clOptionDialogue->LanguageCode = 12;
		$clOptionDialogue->WithFieldStateControl = 1;
		$clOptionDialogue->ReturnXSD = 1;

		return $clOptionDialogue;
	}

	/**
	 * Génère les paramètres pour la méthode GetTokenSession
	 *
	 * @param $UsernameToken : utilisateur NOUTOnline
	 * @param null $UserExtranet : utilisateur extranet
	 * @param null $FormExtranet : formulaire pour l'utilisateur extranet
	 * @return GetTokenSession
	 */
	protected function _getGetTokenSession($UsernameToken, $UserExtranet=null, $FormExtranet=null)
	{
		//il faut retourner les paramètres pour la connexion
		$clGetTokenSession = new GetTokenSession();
		$clGetTokenSession->DefaultClientLanguageCode=12;
		$clGetTokenSession->UsernameToken = $UsernameToken;

		if ($UserExtranet == null)
			$clGetTokenSession->ExtranetUser = null;
		else
		{
			$clGetTokenSession->ExtranetUser = new ExtranetUserType();
			$clGetTokenSession->ExtranetUser->UsernameToken = $UserExtranet;
			$clGetTokenSession->ExtranetUser->Form = $FormExtranet;
		}

		return $clGetTokenSession;
	}

	/**
	 * @param $sTokenSession
	 * @param null $nIDContexteAction
	 * @return array
	 */
	protected function _aGetTabHeader($sTokenSession, $nIDContexteAction=null)
	{
		$clUsernameToken = $this->_clGetUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession, 'OptionDialogue'=>$this->_clGetOptionDialogue());

		if (isset($nIDContexteAction))
			$TabHeader['ActionContext']=$nIDContexteAction;

		return $TabHeader;
	}

	/**
	 * Test l'identification avec des valeurs correctes
	 * @return string
	 */
	public function testGetTokenSession_OK()
	{
		$clReponseWS = $this->m_clNOUTOnline->GetTokenSession($this->_getGetTokenSession($this->_clGetUsernameToken()));
		$sTokenSession = $clReponseWS->sGetTokenSession();
		$this->assertNotEquals('', $sTokenSession);
		return $sTokenSession;
	}


	/**
	 * Test de la fermeture d'une session
	 * @return boolean
	 */
	public function testDisconnect_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		//mot de passe faux
		$nExceptionCode=0;
		try{
			$this->m_clNOUTOnline->disconnect($this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$nExceptionCode=$e->getCode();
		}
		$this->assertEquals(0, $nExceptionCode);

		return !$this->m_clNOUTOnline->getXMLResponseWS()->bIsFault();
	}

	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 */
	protected function _Validate($sTokenSession, $nIDContexteAction)
	{
		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->validate($this->_aGetTabHeader($sTokenSession, $nIDContexteAction));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
	}

	/**
	 * @param $sTokenSession
	 * @param $sActionContexte
	 * @param MessageBox $clMessageBox
	 */
	protected function _ConfirmResponse($sTokenSession, $sActionContexte, MessageBox $clMessageBox)
	{
		$clConfirm = new ConfirmResponse();
		$clConfirm->TypeConfirmation = array_key_exists(MessageBox::IDYES, $clMessageBox->m_TabButton) ? MessageBox::IDYES : MessageBox::IDOK;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->ConfirmResponse($clConfirm, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
	}


	/**
	 * Annule la dernière action ou le contexte d'action entier
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 * @param $bTout
	 */
	protected function _Cancel($sTokenSession, $nIDContexteAction, $bTout)
	{
		$clCancel = new Cancel();
		$clCancel->ByUser=1;
		$clCancel->Context=$bTout ? 1 : 0;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->cancel($clCancel, $this->_aGetTabHeader($sTokenSession, $nIDContexteAction));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
	}


	/**
	 * Test la méthode d'affichage
	 */
	public function testDisplay_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';
		$id = 2;

		$clParamDisplay = new Display();
		$clParamDisplay->Table = $form;
		$clParamDisplay->ParamXML = '<'.$form.'>'.$id.'</'.$form.'>';

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->display($clParamDisplay, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	/**
	 * Test la méthode de récupération d'une valeur d'une colonne d'un enregistrement particulier
	 */
	public function testGetColInRecord_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$clParamGCR = new GetColInRecord();
		$clParamGCR->Column = 'photo utilisateur';
		$clParamGCR->Record = 2;
		$clParamGCR->WantContent = 1;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->getColInRecord($clParamGCR, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function _sExecute($sTokenSession, $sSentence)
	{
		$clParamExecute = new Execute();
		$clParamExecute->Sentence = $sSentence;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->Execute($clParamExecute, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$this->assertNotEquals('', $clReponseWS->sGetActionContext());

		return $clReponseWS;
	}

	/**
	 * Test de la méthode execute (liste utilisateur)
	 */
	public function testExecute_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->_sExecute($sTokenSession, 'liste utilisateur');

		//on valide le contexte
		$this->_Validate($sTokenSession, $clReponseWS->sGetActionContext());

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	/**
	 * @param $sTokenSession
	 * @param $form
	 * @return XMLResponseWS |\NOUT\Bundle\NOUTOnlineBundle\SOAP\ListResponse
	 */
	protected function _sTestList($sTokenSession, $form)
	{
		$clParamList = new ListParams();
		$clParamList->Table = $form;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->listAction($clParamList, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		return $clReponseWS;
	}

	/**
	 * Test de la méthode List (liste utilisateur)
	 */
	public function testList_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWS = $this->_sTestList($sTokenSession, $form);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function _sDrillThrough($sTokenSession, $sActionContext, $sColonne, $sEnreg)
	{
		$clParamDrillThrough = new DrillThrough();
		$clParamDrillThrough->Record = $sEnreg;
		$clParamDrillThrough->Column = $sColonne;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->drillThrough($clParamDrillThrough, $this->_aGetTabHeader($sTokenSession, $sActionContext));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
		$this->assertEquals(XMLResponseWS::RETURNTYPE_RECORD, $clReponseWS->sGetReturnType());

		return $clReponseWS;



	}

	public function testDrillThrough_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();


		//execute
		$clReponseWSList = $this->_sExecute($sTokenSession, 'Afficher Nb Jour d\'absence par contact');

		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le résultat
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList->sGetReturnType(), $clReponseWSList->getNodeXML(), $clReponseWSList->getNodeSchema());

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);
		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseWSList->clGetForm()->getID());


		//le drillthrough

		$clReponseWSDrill = $this->_sDrillThrough($sTokenSession, $sActionContexte, $TabIDColonne[0], $TabIDEnreg[0]);
		$sActionContexteDrill = $clReponseWSDrill->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($sTokenSession, $sActionContexte);

		if ($sActionContexte != $sActionContexteDrill)
			$this->_sCancel($sTokenSession, $sActionContexteDrill);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);

	}

	protected function _sGetCalculation($sTokenSession, $sActionContexte, $TabIDColonne)
	{
		$clParamGetCalculation = new GetCalculation();
		$clParamGetCalculation->ColList=new ColListType($TabIDColonne);
		$clParamGetCalculation->CalculationList=new CalculationListType(
			array(
				CalculationListType::SUM,
				CalculationListType::AVERAGE,
				CalculationListType::MIN,
				CalculationListType::MAX,
				CalculationListType::COUNT
			)
		)
		;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWSCalcul = $this->m_clNOUTOnline->getCalculation($clParamGetCalculation, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		}
		catch(\Exception $e)
		{
			$clReponseWSCalcul = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWSCalcul->bIsFault());
			$nErreur = $clReponseWSCalcul->getNumError();
			$nCategorie = $clReponseWSCalcul->getCatError();
		}
		$this->assertEquals(false, $clReponseWSCalcul->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		return $clReponseWSCalcul;
	}

	/**
	 * Test de la méthode pour récupérer les caculs de fin de liste
	 */
	public function testGetCalculation_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWSList = $this->_sTestList($sTokenSession, $form);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWSList->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList->sGetReturnType(), $clReponseWSList->getNodeXML(), $clReponseWSList->getNodeSchema());

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);

		$clReponseWSCalcul = $this->_sGetCalculation($sTokenSession, $sActionContexte, $TabIDColonne);

		//il faut parser le résultat pour avoir les calculs de fin de liste
		$clReponseWSParser->InitFromXmlXsd($clReponseWSCalcul->sGetReturnType(), $clReponseWSCalcul->getNodeXML(), $clReponseWSCalcul->getNodeSchema());

		$nCalculCount = (int)$clReponseWSParser->m_MapColonne2Calcul[1171]->GetCalcul('count'); //c'est la colonne pseudo
		$nCountNbTotal = $clReponseWSList->clGetCount()->m_nNbTotal;
		$this->assertEquals($nCountNbTotal, $nCalculCount);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function _sRequest($sTokenSession, $table, $sCondList)
	{
		$clParamRequest = new Request();
		$clParamRequest->CondList = $sCondList;
		$clParamRequest->Table = $table;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->request($clParamRequest, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		return $clReponseWS;
	}

	/**
	 * Test de la méthode request (requete des utilisateurs invalide)
	 */
	public function testRequest_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$clFileNPI = new ConditionFileNPI();
		$clFileNPI->EmpileCondition('Invalide', ConditionColonne::COND_EQUAL, 1);
		$CondList = $clFileNPI->sToSoap();

		$table = 'utilisateur';

		$clReponseWS = $this->_sRequest($sTokenSession, $table, $CondList);

		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	protected function _sSearch($sTokenSession, $form)
	{
		$clParamSearch = new Search();
		$clParamSearch->Table = $form;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->listAction($clParamSearch, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		return $clReponseWS;
	}

	public function testSearch_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWS = $this->_sSearch($sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Cancel($sTokenSession, $sActionContexte, false);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function __sCreate($sTokenSession, $form)
	{
		//l'action create
		$clParamCreate = new Create();
		$clParamCreate->Table = $form;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->create($clParamCreate, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		return $clReponseWS;
	}


	protected function _sUpdate($sTokenSession, $sActionContexte, $form, $enreg, $TabCol2Val)
	{
		$clParamUpdate = new Update();
		$clParamUpdate->Table = $form;
		$clParamUpdate->ParamXML = "<id_$form>".$enreg."</id_$form>";
		$clParamUpdate->UpdateData = "<xml><id_$form id=\"".$enreg."\">";

		foreach($TabCol2Val as $colonne=>$valeur)
			$clParamUpdate->UpdateData.="<id_$colonne>".htmlspecialchars($valeur)."</id_$colonne>";

		$clParamUpdate->UpdateData.="</id_$form></xml>";

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->update($clParamUpdate, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		return $clReponseWS;
	}

	protected function _sCreate($sTokenSession, $form, $colonne)
	{
		$clReponseWS = $this->__sCreate( $sTokenSession, $form);


		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS->sGetReturnType(), $clReponseWS->getNodeXML(), $clReponseWS->getNodeSchema());

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$this->assertNotNull($clRecord);

		//on fait l'update
		$sIDEnreg = $clReponseWS->clGetElement()->getID();

		//l'update
		$this->_sUpdate($sTokenSession, $sActionContexte, $form, $sIDEnreg, array($colonne=>'phpUnit Test Create'));

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		return $sIDEnreg;
	}

	public function testCreate_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$colonne = '45208949043557'; //libelle

		$sIDEnreg = $this->_sCreate($sTokenSession, $form, $colonne);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);

		return $sIDEnreg;
	}

	protected function _sSelectForm($sTokenSession, $sActionContexte, $form)
	{
		$clParamSelectForm = new SelectForm();
		$clParamSelectForm->Form = $form;

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->selectForm($clParamSelectForm, $this->_aGetTabHeader($sTokenSession, $sActionContexte));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
		$this->assertEquals(XMLResponseWS::RETURNTYPE_RECORD, $clReponseWS->sGetReturnType());

		return $clReponseWS;
	}

	public function testSelectForm_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '48918773563102';

		$clReponseXML = $this->__sCreate($sTokenSession, $form);
		$sActionContexte = $clReponseXML->sGetActionContext();
		$this->assertEquals(XMLResponseWS::RETURNTYPE_AMBIGUOUSACTION, $clReponseXML->sGetReturnType());

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseXML->sGetReturnType(), $clReponseXML->getNodeXML(), $clReponseXML->getNodeSchema());

		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseXML->clGetForm()->getID());

		//le selectForm en réponse du retour d'action ambigue
		$this->_sSelectForm($sTokenSession, $sActionContexte, $TabIDEnreg[0]);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function _sModify($sTokenSession, $form, $id)
	{
		$clParamModify = new Modify();
		$clParamModify->Table = $form;
		$clParamModify->ParamXML = "<id_$form>$id</id_$form>";

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->modify($clParamModify, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		return $clReponseWS;
	}

	public function testModify_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$id = '219237638150324';
		$colonne = '45208949043557';

		//l'action modify
		$clReponseWS = $this->_sModify($sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();


		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS->sGetReturnType(), $clReponseWS->getNodeXML(), $clReponseWS->getNodeSchema());

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$this->assertNotNull($clRecord);

		$sValeur = $clRecord->sGetValCol($colonne);

		//on fait l'update
		$clReponseWS = $this->_sUpdate($sTokenSession, $sActionContexte, $form, $id, array($colonne=>$sValeur.'t'));

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}

	protected function _sDelete($sTokenSession, $form, $id)
	{
		$clParamDelete = new Delete();
		$clParamDelete->Table = $form;
		$clParamDelete->ParamXML="<id_$form>".$id."</id_$form>";

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->delete($clParamDelete, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		return $clReponseWS;
	}

	public function testDelete_OK()
	{

		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$colonne = '45208949043557';
		$sIDEnreg = $this->_sCreate($form, $colonne, $sTokenSession);

		//l'action delete
		$clReponseWS = $this->_sDelete($sTokenSession, $form, $sIDEnreg);
		$sActionContexte = $clReponseWS->sGetActionContext();

		$this->assertEquals(XMLResponseWS::RETURNTYPE_MESSAGEBOX, $clReponseWS->sGetReturnType());

		//on valide le contexte
		$this->_ConfirmResponse($sTokenSession, $sActionContexte, $clReponseWS->clGetMessageBox());

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testGetStartAutomatism_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();


		$clParamStart = new GetStartAutomatism();

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->getStartAutomatism($clParamStart, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Cancel($sTokenSession, $sActionContexte, false);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testGetTemporalAutomatism_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();


		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->getTemporalAutomatism($this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);
		$this->assertEquals(XMLResponseWS::RETURNTYPE_EMPTY, $clReponseWS->sGetReturnType());

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}



	public function testGetEndAutomatism_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();


		$clParamEnd = new GetEndAutomatism();

		$nErreur=0;
		$nCategorie=0;
		try
		{
			$clReponseWS = $this->m_clNOUTOnline->getEndAutomatism($clParamEnd, $this->_aGetTabHeader($sTokenSession));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();
		}


		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Cancel($sTokenSession, $sActionContexte, false);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}



} 