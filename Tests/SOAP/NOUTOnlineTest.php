<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ReorderSubList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SetOrderSubList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
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
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetChart;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetPlanningInfo;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTableChild;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\PrintParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\RequestParam;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectForm;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectItems;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SelectPrintTemplate;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\TransformInto;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;

/**
 * Class NOUTOnlineTest
 * classe pour tester NOUTOnline en mode Intranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 *
 * bin\phpunit -c app --filter testCreate_OK
 */
class NOUTOnlineTest extends \PHPUnit_Framework_TestCase
{
	protected $m_clConfig;
	protected $m_clNOUTOnline;

	protected $m_sService='127.0.0.1';
	protected $m_sPlagePort='6';


	public function __construct()
	{
		//on instancie la configuration de NOUTOnline
		$this->m_clConfig = new ConfigurationDialogue($this->m_sService, '80'.$this->m_sPlagePort.'2','http://');

		// Create the logger
		$monologger = new Logger('phpunit_log');
		// Now add some handlers
		$monologger->pushHandler(new StreamHandler(__DIR__.'/phpunit.log', Logger::DEBUG));

		//le logger
		$clLogger = new NOUTOnlineLogger($monologger, false);

		//ici on instancie NOUTOnline
		$this->m_clNOUTOnline = new OnlineServiceProxy($this->m_clConfig, $clLogger, null);
	}

	protected function _nGetNbSessionEnCours()
	{
		$opts = array(
			'http'=>array(
				'method'=>"GET",
				'header'=>"Authorization:	Basic YWRtaW46YWRtaW4=\r\n"
			)
		);

		$context = stream_context_create($opts);
		$sRequete = 'http://'.$this->m_sService.':80'.$this->m_sPlagePort.'0/parametre/etat/session/liste/get';

		$json = file_get_contents($sRequete, false, $context);
		$Tab = json_decode($json);

		return count($Tab);
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
		$clOptionDialogue->InitDefault();
		$clOptionDialogue->DisplayValue = OptionDialogue::DISPLAY_No_ID;
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

		if (is_null($UserExtranet))
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

	protected function _Disconnect($sTokenSession, $nNbSession)
	{
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
		$this->assertEquals(false, $this->m_clNOUTOnline->getXMLResponseWS()->bIsFault());


		sleep(20);
		$this->assertEquals($nNbSession, $this->_nGetNbSessionEnCours(), "Session $sTokenSession non fermee");
	}


	/**
	 * Test de la fermeture d'une session
	 * @return boolean
	 */
	public function testDisconnect_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$this->_Disconnect($sTokenSession, $nNbSession);
	}


	protected function __CallProxyFunction($function, $Param, $sTokenSession, $sContexteAction, $bTestContexte, $sReturnType)
	{
		$nErreur=0;
		$nCategorie=0;
		try
		{
			if (!isset($Param))
				$clReponseWS = $this->m_clNOUTOnline->$function($this->_aGetTabHeader($sTokenSession, $sContexteAction));
			else
				$clReponseWS = $this->m_clNOUTOnline->$function($Param, $this->_aGetTabHeader($sTokenSession, $sContexteAction));
		}
		catch(\Exception $e)
		{
			$clReponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $clReponseWS->bIsFault());
			$nErreur = $clReponseWS->getNumError();
			$nCategorie = $clReponseWS->getCatError();

			echo $clReponseWS->sGetXML();
		}

		$this->assertEquals(false, $clReponseWS->bIsFault());
		$this->assertEquals(0, $nErreur);
		$this->assertEquals(0, $nCategorie);

		if ($bTestContexte)
			$this->assertNotEquals('', $clReponseWS->sGetActionContext());

		if (isset($sReturnType))
			$this->assertEquals($sReturnType, $clReponseWS->sGetReturnType());

		return $clReponseWS;
	}


	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 */
	protected function _Validate($sTokenSession, $nIDContexteAction)
	{
		return $this->__CallProxyFunction('validate', null, $sTokenSession, $nIDContexteAction, false, null);
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

		return $this->__CallProxyFunction('ConfirmResponse', $clConfirm, $sTokenSession, $sActionContexte, false, null);
	}


	/**
	 * Annule la dernière action ou le contexte d'action entier
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 * @param $bTout
	 */
	protected function _sCancel($sTokenSession, $sActionContexte, $bTout)
	{
		$clCancel = new Cancel();
		$clCancel->ByUser=1;
		$clCancel->Context=$bTout ? 1 : 0;

		return $this->__CallProxyFunction('cancel', $clCancel, $sTokenSession, $sActionContexte, false, null);
	}


	/**
	 * Test la méthode d'affichage
	 */
	public function testDisplay_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';
		$id = 2;

		$clParamDisplay = new Display();
		$clParamDisplay->Table = $form;
		$clParamDisplay->ParamXML = '<'.$form.'>'.$id.'</'.$form.'>';

		$clReponseWS = $this->__CallProxyFunction('display', $clParamDisplay, $sTokenSession, '', true, XMLResponseWS::RETURNTYPE_RECORD);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	/**
	 * @param $sTokenSession
	 * @param $form
	 * @param $id
	 * @return XMLResponseWS
	 */
	protected function _sPrint($sTokenSession, $form, $id)
	{
		$clParamPrint = new PrintParams();
		$clParamPrint->Table = $form;
		$clParamPrint->ParamXML = '<id_'.$form.'>'.$id.'</id_'.$form.'>';

		return $this->__CallProxyFunction('printAction', $clParamPrint, $sTokenSession, '', true, null);
	}

	public function testPrint_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->_sPrint($sTokenSession, '1169', 2);

		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		$clData = $clReponseWSParser->clGetData(0);
		$html_raw = $clData->sGetRaw();

		$sXML = utf8_encode(file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/print/utilisateur_2.html'));
		$sXML=utf8_decode(str_replace('§§DateDuJour§§', date('d/m/Y') , $sXML));

		$this->assertEquals(str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$sXML), str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$html_raw));

		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	/**
	 * @param $sTokenSession
	 * @param $sContexteAction
	 * @return XMLResponseWS
	 */
	protected function _sHasChanged($sTokenSession, $sContexteAction)
	{
		return $this->__CallProxyFunction('hasChanged', null, $sTokenSession, $sContexteAction, true, XMLResponseWS::RETURNTYPE_VALUE);
	}


	public function testHasChanged_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->__sCreate($sTokenSession, '41296233836619',  XMLResponseWS::RETURNTYPE_RECORD);
		$sActionContexte = $clReponseWS->sGetActionContext();

//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if (isset($clRecord))
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($sTokenSession, $sActionContexte, '41296233836619', $clRecord->m_nIDEnreg, array('45208949043557'=>'phpUnit Test HasChanged'));

			//on fait un has changed pour pouvoir imprimer après
			$clReponseWS = $this->_sHasChanged($sTokenSession, $sActionContexte);
			$this->assertEquals(1, $clReponseWS->getValue());

			if ($clReponseWS->getValue() == 1)
			{
				//on valide
				$this->_Validate($sTokenSession, $sActionContexte);

				//et on imprime
				$clReponseWS=$this->_sPrint($sTokenSession, '41296233836619', $clRecord->m_nIDEnreg);
				$clReponseWSParser = new ReponseWSParser();
				$clReponseWSParser->InitFromXmlXsd($clReponseWS);

				$clData = $clReponseWSParser->clGetData(0);
				$html_raw = $clData->sGetRaw();
			}
		}

		$sXML = utf8_encode(file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/print/has_changed.html'));
		$sXML=utf8_decode(str_replace('§§DateDuJour§§', date('d/m/Y') , $sXML));

		$this->assertEquals(str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$sXML), str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$html_raw));

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);

	}

	/**
	 * @param $sTokenSession
	 * @return XMLResponseWS
	 */
	protected function _sSelectPrintTemplate($sTokenSession, $sActionContexte, $modele)
	{
		$clParamSelectPrintTemplate = new SelectPrintTemplate();
		$clParamSelectPrintTemplate->Template = $modele;

		return $this->__CallProxyFunction('selectPrintTemplate', $clParamSelectPrintTemplate, $sTokenSession, $sActionContexte, false, null);
	}


	public function testSelectPrintTemplate_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		//GUID : "1DJVR37SET", 50165053649373 [F_TABLEAU]
		$clReponseWSPrint = $this->_sPrint($sTokenSession, '50165053649373', '37658108898600');

		//il faut vérifier le type de retour
		$this->assertEquals(XMLResponseWS::RETURNTYPE_PRINTTEMPLATE, $clReponseWSPrint->sGetReturnType());

		$clReponseWSParserPrint = new ReponseWSParser();
		$clReponseWSParserPrint->InitFromXmlXsd($clReponseWSPrint);

		//il faut vérifier l'existance des enregistrements suivant dans la liste des templates
		//39234361897596 (Form avec ME Titre Bleu - Vrai)
		//41291660900316 (HTML Form avec ME Titre Jaune)
		//201116455061095 (Form Avec ME Avec Section Odt)
		//42636439109550 (Form Avec ME Odt)
		//46227041313139 (Form Avec ME Odt Evo)
		//46360185592694 (Form Avec ME Odt Local)

		$TabIDTemplate = $clReponseWSParserPrint->GetTabIDEnregFromForm($clReponseWSPrint->clGetForm()->getID());
		$this->assertContains('39234361897596', $TabIDTemplate);
		$this->assertContains('41291660900316', $TabIDTemplate);
		$this->assertContains('201116455061095', $TabIDTemplate);
		$this->assertContains('42636439109550', $TabIDTemplate);
		$this->assertContains('46227041313139', $TabIDTemplate);
		$this->assertContains('46360185592694', $TabIDTemplate);

		$clReponseWSSelect = $this->_sSelectPrintTemplate($sTokenSession, $clReponseWSPrint->sGetActionContext(), '41291660900316');

		$clReponseWSParserSelect = new ReponseWSParser();
		$clReponseWSParserSelect->InitFromXmlXsd($clReponseWSSelect);
		$clData = $clReponseWSParserSelect->clGetData(0);
		$html_raw = $clData->sGetRaw();

		$sXML = utf8_encode(file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/print/form avec me.html'));
		$sXML=utf8_decode(str_replace('§§DateDuJour§§', date('d/m/Y') , $sXML));

		$this->assertEquals(str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$sXML), str_replace(array(' ', "\t", "\r", "\n"), array("", "", "", ""),$html_raw));

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sGetColInRecord($sTokenSession, $sColonne, $sRecord, $bWantContent)
	{
		$clParamGCR = new GetColInRecord();
		$clParamGCR->Column = $sColonne;
		$clParamGCR->Record = $sRecord;
		$clParamGCR->WantContent = $bWantContent;

		return $this->__CallProxyFunction('getColInRecord', $clParamGCR, $sTokenSession, '', true, null);

	}

	/**
	 * Test la méthode de récupération d'une valeur d'une colonne d'un enregistrement particulier
	 */
	public function testGetColInRecord_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->_sGetColInRecord($sTokenSession, 'photo utilisateur', 2, 1);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sExecute($sTokenSession, $sSentence, $nIDAction, $bTestContexte)
	{
		$clParamExecute = new Execute();
		$clParamExecute->Sentence = $sSentence;
		$clParamExecute->ID = $nIDAction;

		return $this->__CallProxyFunction('Execute', $clParamExecute, $sTokenSession, '', $bTestContexte, null);
	}

	/**
	 * Test de la méthode execute (liste utilisateur)
	 */
	public function testExecute_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->_sExecute($sTokenSession, 'liste utilisateur', '', true);

		//on valide le contexte
		$this->_Validate($sTokenSession, $clReponseWS->sGetActionContext());

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	/**
	 * @param $sTokenSession
	 * @param $form
	 * @return XMLResponseWS |\NOUT\Bundle\NOUTOnlineBundle\SOAP\ListResponse
	 */
	protected function _sList($sTokenSession, $form, $sActionContexte='', $displayMode=OnlineServiceProxy::DISPLAYMODE_Liste)
	{
		$clParamList = new ListParams();
		$clParamList->Table = $form;
		$clParamList->DisplayMode = $displayMode;

		return $this->__CallProxyFunction('listAction', $clParamList, $sTokenSession, $sActionContexte, true, null);
	}

	/**
	 * Test de la méthode List (liste utilisateur)
	 */
	public function testList_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWS = $this->_sList($sTokenSession, $form);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sDrillThrough($sTokenSession, $sActionContext, $sColonne, $sEnreg)
	{
		$clParamDrillThrough = new DrillThrough();
		$clParamDrillThrough->Record = $sEnreg;
		$clParamDrillThrough->Column = $sColonne;

		$clReponseWS =  $this->__CallProxyFunction('drillThrough', $clParamDrillThrough, $sTokenSession, $sActionContext, true, null);

		$this->assertEquals(XMLResponseWS::RETURNTYPE_RECORD, $clReponseWS->sGetReturnType());

		return $clReponseWS;



	}

	public function testDrillThrough_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		//execute
		$clReponseWSList = $this->_sExecute($sTokenSession, 'Afficher Nb Jour d\'absence par contact', '', true);

		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le résultat
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList);

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);
		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseWSList->clGetForm()->getID());


		//le drillthrough

		$clReponseWSDrill = $this->_sDrillThrough($sTokenSession, $sActionContexte, $TabIDColonne[0], $TabIDEnreg[0]);
		$sActionContexteDrill = $clReponseWSDrill->sGetActionContext();

		//annulation de la liste
		$this->_sCancel($sTokenSession, $sActionContexte, false);

		if ($sActionContexte != $sActionContexteDrill)
			$this->_sCancel($sTokenSession, $sActionContexteDrill, false);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);

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

		return  $this->__CallProxyFunction('getCalculation', $clParamGetCalculation, $sTokenSession, $sActionContexte, true, null);
	}

	/**
	 * Test de la méthode pour récupérer les caculs de fin de liste
	 */
	public function testGetCalculation_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWSList = $this->_sList($sTokenSession, $form);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWSList->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList);

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);

		$clReponseWSCalcul = $this->_sGetCalculation($sTokenSession, $sActionContexte, $TabIDColonne);

		//il faut parser le résultat pour avoir les calculs de fin de liste
		$clReponseWSParser->InitFromXmlXsd($clReponseWSCalcul);

		$nCalculCount = (int)$clReponseWSParser->m_MapColonne2Calcul[1171]->GetCalcul('count'); //c'est la colonne pseudo
		$nCountNbTotal = $clReponseWSList->clGetCount()->m_nNbTotal;
		$this->assertEquals($nCountNbTotal, $nCalculCount);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}



	/**
	 * @param $sTokenSession
	 * @param $sIDActionContexte
	 * @param $form
	 * @param $index
	 * @return XMLResponseWS
	 */
	protected function _sSelectItems($sTokenSession, $sIDActionContexte, $TabSelection)
	{
		$clParamSelectItems = new SelectItems();
		$clParamSelectItems->items = implode('|',$TabSelection);

		return  $this->__CallProxyFunction('selectItems', $clParamSelectItems, $sTokenSession, $sIDActionContexte, true, null);
	}

	/**
	 * @param OnlineServiceProxy $OnlineProxy
	 * @param $sTokenSession
	 * @param $sIDActionContexte
	 * @return XMLResponseWS
	 */
	protected function _sGetChart($sTokenSession, $sIDActionContexte, $form, $index)
	{
		$clParamChart = new GetChart();
		$clParamChart->Height = 500;
		$clParamChart->Width = 700;
		$clParamChart->DPI = 72;
		$clParamChart->Index = $index;
		$clParamChart->Table = $form;

		return  $this->__CallProxyFunction('getChart', $clParamChart, $sTokenSession, $sIDActionContexte, true, null);
	}


	/**
	 * Test de la méthode pour récupérer les caculs de fin de liste
	 */
	public function testGetChart_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '42704702455450';

		$clReponseWSList = $this->_sList($sTokenSession, $form);

		//vérification du contexte d'action
		$sActionContexte = $clReponseWSList->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on vérifie que le mode graphe est disponible
		$TabPossibleDM = $clReponseWSList->GetTabPossibleDisplayMode();
		$this->assertContains(OnlineServiceProxy::DISPLAYMODE_Graphe, $TabPossibleDM);

		$clParserList = new ReponseWSParser();
		$clParserList->InitFromXmlXsd($clReponseWSList);

		$TabIDEnreg = array_slice($clParserList->GetTabEnregTableau()->GetTabIDEnreg($clReponseWSList->clGetForm()->getID()), 0, 5);
		$clReponseWSSelectItems = $this->_sSelectItems($sTokenSession, $sActionContexte, $TabIDEnreg);
		$this->assertEquals(XMLResponseWS::RETURNTYPE_EMPTY, $clReponseWSSelectItems->sGetReturnType());

		$clReponseWSGraphe = $this->_sList($sTokenSession, $form, $sActionContexte, OnlineServiceProxy::DISPLAYMODE_Graphe);
		$nNbChart = $clReponseWSGraphe->nGetNumberOfChart();
		$this->assertNotEquals(0, $nNbChart);

		$clReponseWSChart = $this->_sGetChart($sTokenSession, $sActionContexte, $form, 1);
		$clParser = new ReponseWSParser();
		$clParser->InitFromXmlXsd($clReponseWSChart);

		$this->assertNotNull($clParser->m_clChart);
		$this->assertNotEquals('', $clParser->m_clChart->m_sType);
		$this->assertNotEquals('', $clParser->m_clChart->m_sTitre);
		$this->assertNotEmpty($clParser->m_clChart->m_TabAxes);
		$this->assertNotEmpty($clParser->m_clChart->m_TabSeries);

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}





	/**
	 * @Route("/chart/{form}/{host}", name="chart", defaults={"host"=""})
	 */
	public function chartAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->_clGetOnlineProxy($host);

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWSList = $this->_sList($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWSList->sGetActionContext();

		$TabPossibleDM = $clReponseWSList->GetTabPossibleDisplayMode();
		if (in_array(OnlineServiceProxy::DISPLAYMODE_Graphe, $TabPossibleDM))
		{
			$clReponseWSGraphe = $this->_sList($OnlineProxy, $sTokenSession, $form, $sActionContexte, OnlineServiceProxy::DISPLAYMODE_Graphe);
			$nNbChart = $clReponseWSGraphe->nGetNumberOfChart();

			for ($i=0 ; $i<$nNbChart ; $i++)
			{
				$clReponseWSChart = $this->_sGetChart($OnlineProxy, $sTokenSession, $sActionContexte, $form, $i+1);
				$clParser = new ReponseWSParser();
				$clParser->InitFromXmlXsd($clReponseWSChart);
				$this->_VarDumpRes('Chart', $clParser->m_clChart);
			}
		}
		else
		{
			echo '<pre>Affichage des graphes non possible</pre>';
		}

		//annulation de la liste
		$this->_sCancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}




	protected function _sRequest($sTokenSession, $table, $sCondList)
	{
		$clParamRequest = new Request();
		$clParamRequest->CondList = $sCondList;
		$clParamRequest->Table = $table;

		return  $this->__CallProxyFunction('request', $clParamRequest, $sTokenSession, '', false, XMLResponseWS::RETURNTYPE_LIST);
	}

	/**
	 * Test de la méthode request (requete des utilisateurs invalide)
	 */
	public function testRequest_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
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
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sRequestParam($sTokenSession, $table, $sCondList)
	{
		$clParamRequest = new RequestParam();
		$clParamRequest->CondList = $sCondList;
		$clParamRequest->Table = $table;

		return  $this->__CallProxyFunction('requestParam', $clParamRequest, $sTokenSession, '', false, XMLResponseWS::RETURNTYPE_LIST);
	}


	public function testRequestParam_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clFileNPI = new ConditionFileNPI();
		$clFileNPI->EmpileCondition('8521', ConditionColonne::COND_EQUAL, 8267);
		$CondList = $clFileNPI->sToSoap();
		$table = '8267';

		$clReponseWS = $this->_sRequestParam($sTokenSession, $table, $CondList);

		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sSearch($sTokenSession, $form)
	{
		$clParamSearch = new Search();
		$clParamSearch->Table = $form;

		return  $this->__CallProxyFunction('search', $clParamSearch, $sTokenSession, '', true, XMLResponseWS::RETURNTYPE_LIST);
	}

	public function testSearch_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = 'utilisateur';

		$clReponseWS = $this->_sSearch($sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_sCancel($sTokenSession, $sActionContexte, false);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function __sCreate($sTokenSession, $form, $sTypeRetourAttendu)
	{
		//l'action create
		$clParamCreate = new Create();
		$clParamCreate->Table = $form;

		return  $this->__CallProxyFunction('create', $clParamCreate, $sTokenSession, '', true, $sTypeRetourAttendu);
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

		return  $this->__CallProxyFunction('update', $clParamUpdate, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_RECORD);
	}

	protected function _sCreate($sTokenSession, $form, $colonne, $valeur)
	{
		$clReponseWS = $this->__sCreate( $sTokenSession, $form, XMLResponseWS::RETURNTYPE_RECORD);


		//vérification du contexte d'action
		$sActionContexte = $clReponseWS->sGetActionContext();
		$this->assertNotEquals('', $sActionContexte);

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$this->assertNotNull($clRecord);

		//on fait l'update
		$sIDEnreg = $clReponseWS->clGetElement()->getID();

		//l'update
		$this->_sUpdate($sTokenSession, $sActionContexte, $form, $sIDEnreg, array($colonne=>$valeur));

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		return $sIDEnreg;
	}

	public function testCreate_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$colonne = '45208949043557'; //libelle

		$sIDEnreg = $this->_sCreate($sTokenSession, $form, $colonne, 'phpUnit Test Create');

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}


	/**
	 * @param $sTokenSession
	 * @return XMLResponseWS
	 */
	protected function _sTransformInto($sTokenSession, $formDest, $formSrc, $elemSrc)
	{
		$clParamTransformInto = new TransformInto();
		$clParamTransformInto->Table=$formDest;
		$clParamTransformInto->TableSrc = $formSrc;
		$clParamTransformInto->ElemSrc = $elemSrc;

		return  $this->__CallProxyFunction('transformInto', $clParamTransformInto, $sTokenSession, '', true, XMLResponseWS::RETURNTYPE_RECORD);
	}



	/**
	 * @Route("/transform_into/{formSrc}/{formDest}/{colonne}/{valeur}/{host}", name="transform_into", defaults={"host"=""})
	 *
	 * exemple GUID : /transform_into/51346223489588/40810668714136/40896568059607/trois
	 */
	public function testTransformInto_OK()
	{
		//la connexion
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$formSrc = '51346223489588'; //Creation - Fils 1
		$formDest = '40810668714136'; //Creation - Fils 2

		$sIDEnreg = $this->_sCreate($sTokenSession, '51346223489588', '40896568059607', 'phpUnit Test transformer En');

		//et on le transforme en formulaire 2
		$clReponseWS = $this->_sTransformInto($sTokenSession, $formDest, $formSrc, $sIDEnreg);
		$this->assertEquals($formDest, $clReponseWS->clGetForm()->getID());

		$this->_Validate($sTokenSession, $clReponseWS->sGetActionContext());

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}




	protected function _sSelectForm($sTokenSession, $sActionContexte, $form)
	{
		$clParamSelectForm = new SelectForm();
		$clParamSelectForm->Form = $form;

		return  $this->__CallProxyFunction('selectForm', $clParamSelectForm, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_RECORD);
	}

	public function testSelectForm_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '48918773563102';

		$clReponseXML = $this->__sCreate($sTokenSession, $form, XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION);
		$sActionContexte = $clReponseXML->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseXML);

		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseXML->clGetForm()->getID());

		//le selectForm en réponse du retour d'action ambigue
		$this->_sSelectForm($sTokenSession, $sActionContexte, $TabIDEnreg[0]);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sModify($sTokenSession, $form, $id)
	{
		$clParamModify = new Modify();
		$clParamModify->Table = $form;
		$clParamModify->ParamXML = "<id_$form>$id</id_$form>";

		return  $this->__CallProxyFunction('modify', $clParamModify, $sTokenSession, '', true, XMLResponseWS::RETURNTYPE_RECORD);
	}

	public function testModify_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$id = '219237638150324';
		$colonne = '45208949043557';

		//l'action modify
		$clReponseWS = $this->_sModify($sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();


		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS);

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		$this->assertNotNull($clRecord);

		$sValeur = $clRecord->getValCol($colonne);

		//on fait l'update
		$clReponseWS = $this->_sUpdate($sTokenSession, $sActionContexte, $form, $id, array($colonne=>$sValeur.'t'));

		//on valide le contexte
		$this->_Validate($sTokenSession, $sActionContexte);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sDelete($sTokenSession, $form, $id)
	{
		$clParamDelete = new Delete();
		$clParamDelete->Table = $form;
		$clParamDelete->ParamXML="<id_$form>".$id."</id_$form>";

		return  $this->__CallProxyFunction('delete', $clParamDelete, $sTokenSession, '', true, XMLResponseWS::RETURNTYPE_MESSAGEBOX);
	}

	public function testDelete_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$form = '41296233836619'; //formulaire avec liste images
		$colonne = '45208949043557';
		$sIDEnreg = $this->_sCreate($sTokenSession, $form, $colonne, 'phpUnit Test Delete');

		//l'action delete
		$clReponseWS = $this->_sDelete($sTokenSession, $form, $sIDEnreg);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide le contexte
		$this->_ConfirmResponse($sTokenSession, $sActionContexte, $clReponseWS->clGetMessageBox());

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sEnterReorderListMode($sTokenSession, $sActionContexte)
	{
		return  $this->__CallProxyFunction('enterReorderListMode', null, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_VALUE);
	}

	protected function _sSetOrderList($sTokenSession, $sActionContexte, $TabIDEnreg, $nOffset)
	{
		$clSetOrderList = new SetOrderList($TabIDEnreg, $nOffset);
		return  $this->__CallProxyFunction('setOrderList', $clSetOrderList, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_VALUE);
	}

	protected function _sReOrderList($sTokenSession, $sActionContexte, $TabIDEnreg, $nScale, $nTypeMove)
	{
		$clReorderList = new ReorderList($TabIDEnreg, $nScale, $nTypeMove);
		return  $this->__CallProxyFunction('reorderList', $clReorderList, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_VALUE);
	}

	public function testOrderList_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		//il faut commencer par réordonner la liste
		$this->_sExecute($sTokenSession, '', '221569603630667', false);

		//on affiche la liste
		$clReponseList = $this->_sList($sTokenSession, '228261139523058');
		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseList);
		$tabEnregTableauOrigine = $clReponseWSParser->GetTabEnregTableau();
		$ListeEnregOrig=implode('|', array_slice($tabEnregTableauOrigine->GetTabIDEnreg(), 0, 5));
		$this->assertEquals('218610348012442|220504428595852|225220302686986|227775829887743|212747717663291', $ListeEnregOrig);


		$clReponseEnter = $this->_sEnterReorderListMode($sTokenSession, $clReponseList->sGetActionContext());
		$this->assertEquals(1, $clReponseEnter->getValue());


		$tabSetOrder = new EnregTableauArray();
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(3), $tabEnregTableauOrigine->nGetIDEnreg(3));
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(2), $tabEnregTableauOrigine->nGetIDEnreg(2));
		$tabSetOrder->Add($tabEnregTableauOrigine->nGetIDTableau(1), $tabEnregTableauOrigine->nGetIDEnreg(1));

		$clReponseSetOrder = $this->_sSetOrderList($sTokenSession, $clReponseList->sGetActionContext(), $tabSetOrder, 1);
		$ListeIntermediaire = implode('|', array_slice(explode('|', $clReponseSetOrder->getValue()),0,5));
		$this->assertEquals('218610348012442|227775829887743|225220302686986|220504428595852|212747717663291', $ListeIntermediaire);

		$tabReorder = new EnregTableauArray();
		$tabReorder->Add($tabEnregTableauOrigine->nGetIDTableau(0), $tabEnregTableauOrigine->nGetIDEnreg(0));

		$clReponseReOrder = $this->_sReOrderList($sTokenSession, $clReponseList->sGetActionContext(), $tabReorder, 4, ReorderList::MOVE_DOWN);
		$ListeIntermediaireFinal = implode('|', array_slice(explode('|', $clReponseReOrder->getValue()),0,5));
		$this->assertEquals('227775829887743|225220302686986|220504428595852|212747717663291|218610348012442', $ListeIntermediaireFinal);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	protected function _sSetOrderSubList($sTokenSession, $sActionContexte, $nIDColonne, $TabIDEnreg, $nOffset)
	{
		$clSetOrderList = new SetOrderSubList($nIDColonne, $TabIDEnreg, $nOffset);
		return  $this->__CallProxyFunction('setOrderSubList', $clSetOrderList, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_VALUE);
	}

	protected function _sReOrderSubList($sTokenSession, $sActionContexte, $nIDColonne, $TabIDEnreg, $nScale, $nTypeMove)
	{
		$clReorderList = new ReorderSubList($nIDColonne, $TabIDEnreg, $nScale, $nTypeMove);
		return  $this->__CallProxyFunction('reorderSubList', $clReorderList, $sTokenSession, $sActionContexte, true, XMLResponseWS::RETURNTYPE_VALUE);
	}

	public function testOrderSubList_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		//modification d'enregistrement
		$clReponseModify = $this->_sModify($sTokenSession, '228261139523058', '218610348012442');
		$sActionContexte = $clReponseModify->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clParserModify = new ReponseWSParser();
		$clParserModify->InitFromXmlXsd($clReponseModify);
		$clRecord = $clParserModify->clGetRecord($clReponseModify->clGetForm(), $clReponseModify->clGetElement());
		$this->assertNotNull($clRecord);

		$TabValColOrig = $clRecord->getValCol('221655479824831');

		$TabSetOrder = $TabValColOrig;
		$sTemp = $TabSetOrder[0];
		$TabSetOrder[0]=$TabSetOrder[3];
		$TabSetOrder[3]=$sTemp;

		$clReponseSetOrder = $this->_sSetOrderSubList($sTokenSession, $sActionContexte, '221655479824831', $TabSetOrder, 0);
		$this->assertEquals(trim(implode('|', $TabSetOrder),'|'), trim($clReponseSetOrder->getValue(), '|'));

		$tabSetOrderRes = new EnregTableauArray();
		$tabSetOrderRes->AddFromListeStr('', $clReponseSetOrder->getValue());
		$tabIDEnreg = array($tabSetOrderRes->nGetIDEnreg(1), $tabSetOrderRes->nGetIDEnreg(3));

		$clReponseReOrder = $this->_sReOrderSubList($sTokenSession, $sActionContexte, '221655479824831', $tabIDEnreg, 1, ReorderList::MOVE_DOWN);

		$TabTemp = $tabSetOrderRes->GetTabIDEnreg();
		$sTemp = $TabTemp[2];
		$TabTemp[2] = $TabTemp[1];
		$TabTemp[1]=$sTemp;

		$sTemp = $TabTemp[4];
		$TabTemp[4] = $TabTemp[3];
		$TabTemp[3]=$sTemp;

		$this->assertEquals(trim(implode('|', $TabTemp),'|'), trim($clReponseReOrder->getValue(), '|'));

		$this->_Validate($sTokenSession, $sActionContexte);

		//vérification
		$clReponseGCIR = $this->_sGetColInRecord($sTokenSession, '221655479824831', '218610348012442', 1);
		$this->_sCancel($sTokenSession, $clReponseGCIR->sGetActionContext(), 1);

		$clParserGCIR = new ReponseWSParser();
		$clParserGCIR->InitFromXmlXsd($clReponseGCIR);
		$clData = $clParserGCIR->clGetData(0);

		$this->assertEquals($clData->m_sContent, $clReponseReOrder->getValue());

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}







	protected function _sGetPlanningInfo($sTokenSession, $Resource, $StartTime, $EndTime)
	{
		$clPlanningInfo = new GetPlanningInfo();
		$clPlanningInfo->Resource = $Resource;
		$clPlanningInfo->StartTime = $StartTime;
		$clPlanningInfo->EndTime = $EndTime;

		return  $this->__CallProxyFunction('getPlanningInfo', $clPlanningInfo, $sTokenSession, '', false, XMLResponseWS::RETURNTYPE_PLANNING);
	}


	public function testGetPlanningInfo_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		//l'action getPlanningInfo
		$clReponseXML = $this->_sGetPlanningInfo($sTokenSession, '36683203627649', '20140901000000', '20140907000000');

		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseXML);

		$this->assertNotEmpty($clReponseWSParser->m_TabEventPlanning);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}


	public function testGetStartAutomatism_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->__CallProxyFunction('getStartAutomatism', new GetStartAutomatism(), $sTokenSession, '', true, null);

		//on valide le contexte
		$this->_sCancel($sTokenSession, $clReponseWS->sGetActionContext(), false);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	public function testGetEndAutomatism_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clReponseWS = $this->__CallProxyFunction('getEndAutomatism', new GetEndAutomatism(), $sTokenSession, '', true, null);

		//on valide le contexte
		$this->_sCancel($sTokenSession, $clReponseWS->sGetActionContext(), false);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}



	public function testGetTemporalAutomatism_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$this->__CallProxyFunction('getTemporalAutomatism', null, $sTokenSession, '', false, XMLResponseWS::RETURNTYPE_EMPTY);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}

	public function testGetLanguages_OK()
	{
		$clReponseWS = $this->__CallProxyFunction('getLanguages', null, '', '', false, XMLResponseWS::RETURNTYPE_EXCEPTION);

		$tabLanguages = $clReponseWS->GetTabLanguages();
		sort($tabLanguages);
		$tabAttendu = array(9, 10, 12);

		$this->assertEquals($tabLanguages, $tabAttendu);
		/*
		<xml>
			<LanguageCode>12</LanguageCode>
			<LanguageCode>9</LanguageCode>
			<LanguageCode>10</LanguageCode>
		</xml>
*/
	}

	public function testGetTableChild_OK()
	{
		$nNbSession = $this->_nGetNbSessionEnCours();
		$sTokenSession = $this->testGetTokenSession_OK();

		$clTableChildParam = new GetTableChild();
		$clTableChildParam->Table = '48918773563102';
		$clTableChildParam->Recursive = 1;
		$clTableChildParam->ReadOnly = 1;

		$this->__CallProxyFunction('getTableChild', $clTableChildParam, $sTokenSession, '', false, XMLResponseWS::RETURNTYPE_LIST);

		//on déconnecte
		$this->_Disconnect($sTokenSession, $nNbSession);
	}





} 