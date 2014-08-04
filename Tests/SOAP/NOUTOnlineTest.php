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
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExtranetUserType;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
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

	protected function _clGetOptionDialogue()
	{
		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->DisplayValue = OnlineServiceProxy::FORMHEAD_UNDECODED_SPECIAL_ELEM;;
		$clOptionDialogue->Readable = 0;
		$clOptionDialogue->EncodingOutput = 0;
		$clOptionDialogue->LanguageCode = 12;
		$clOptionDialogue->WithFieldStateControl = 1;
		$clOptionDialogue->ReturnXSD = 0;

		return $clOptionDialogue;
	}

	/**
	 * Génère les paramètres pour la méthode GetTokenSession
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
	 * Ferme une session
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


	public function testExecute_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$clParamExecute = new Execute();
		$clParamExecute->Sentence = 'liste utilisateur';

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
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testList_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

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

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testRequest_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$clParamRequest = new Request();
		$clParamRequest->CondList = '<Condition><CondCol>Invalide</CondCol><CondType>Equal</CondType><CondValue>1</CondValue></Condition>';
		$clParamRequest->Table = 'utilisateur';

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

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}



	public function testSearch_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

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

		//on valide le contexte
		$this->_Cancel($sTokenSession, $sActionContexte, false);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testModify_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$id = '219237638150324';
		$colonne = '45208949043557';

		//l'action modify
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

		//l'enregistrement retourné
		$clRecord = new Record(Record::LEVEL_RECORD, $clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$clRecord->initFromReponseWS($this->_clGetOptionDialogue(), $clReponseWS->getNodeXML('Modify'), $clReponseWS->getNodeSchema());

		$sValeur = $clRecord->sGetValCol($colonne);

		//on fait l'update

		$clParamUpdate = new Update();
		$clParamUpdate->Table = $form;
		$clParamUpdate->ParamXML = "<id_$form>$id</id_$form>";
		$clParamUpdate->UpdateData = "<xml><id_$form id=\"$id\"><id_$colonne>".htmlentities($sValeur.'t')."</id_$colonne></id_$form></xml>";

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

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);
	}


	public function testCreate_OK()
	{
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$colonne = '45208949043557';

		//l'action modify
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

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//l'enregistrement retourné
		$clRecord = new Record(Record::LEVEL_RECORD, $clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$clRecord->initFromReponseWS($this->_clGetOptionDialogue(), $clReponseWS->getNodeXML('Modify'), $clReponseWS->getNodeSchema());


		//on fait l'update
		$sIDEnreg = $clReponseWS->clGetElement()->getID();

		$clParamUpdate = new Update();
		$clParamUpdate->Table = $form;
		$clParamUpdate->ParamXML = "<id_$form>".$sIDEnreg."</id_$form>";
		$clParamUpdate->UpdateData = "<xml><id_$form id=\"".$sIDEnreg."\"><id_$colonne>test</id_$colonne></id_$form></xml>";

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

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);

		return $sIDEnreg;
	}

	public function testDelete_OK()
	{
		$sIDEnreg = $this->testCreate_OK();

		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images



		//l'action modify
		$clParamDelete = new Delete();
		$clParamDelete->Table = $form;
		$clParamDelete->ParamXML="<id_$form>".$sIDEnreg."</id_$form>";

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

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->testDisconnect_OK($sTokenSession);

		return $sIDEnreg;
	}

} 