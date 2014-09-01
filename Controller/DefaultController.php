<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CalculationListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Header\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ColListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionFileNPI;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionOperateur;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\ReponseWSParser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\MessageBox;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Cancel;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ConfirmResponse;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Create;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CreateFrom;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Delete;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\DrillThrough;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Execute;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetCalculation;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetColInRecord;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetEndAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetStartAutomatism;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Request;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Search;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;


use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// this imports the annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;





/**
 * Class DefaultController
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 */
class DefaultController extends Controller
{
	/**
	 * @Route("/", name="index")
	 */
    public function indexAction()
    {
        return $this->render('NOUTOnlineBundle:Default:index.html.twig');
    }

	protected function _clGetConfiguration($host)
	{
		$sEndPoint = './bundles/noutonline/Service.wsdl';
		$sService = 'http://'.$host;

		//on récupére le prefixe (http | https);
		$sProtocolPrefix = substr($sService,0,strpos($sService,'//')+2 );

		list($sHost,$sPort) = explode(':', str_replace($sProtocolPrefix,'',$sService) );

		$clConfiguration = new ConfigurationDialogue($sEndPoint, true, $sHost, $sPort,$sProtocolPrefix);
		return $clConfiguration;
	}


	protected function _VarDumpRes($sOperation, $ret)
	{
		echo '<h1>'.$sOperation.'</h1>';
		if ($ret instanceof XMLResponseWS)
			echo '<pre>'.htmlentities($ret->sGetXML()).'</pre>';
		else
			var_dump($ret);
	}

	protected function _sConnexion(OnlineServiceProxy $OnlineProxy)
	{
		//GetTokenSession
		$clGetTokenSession = $this->get('nout_online.connection_manager')->getGetTokenSession();
		$clReponseXML = $OnlineProxy->getTokenSession($clGetTokenSession);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML->sGetTokenSession());

		return $clReponseXML->sGetTokenSession();
	}

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

	protected function _TabGetHeader($sTokenSession, $nIDContexteAction=null)
	{
		$clUsernameToken = $this->get('nout_online.connection_manager')->getUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession, 'OptionDialogue'=>$this->_clGetOptionDialogue());

		if (isset($nIDContexteAction))
			$TabHeader['ActionContext']=$nIDContexteAction;

		return $TabHeader;
	}

	protected function _bDeconnexion(OnlineServiceProxy $OnlineProxy, $sTokenSession)
	{
		//récupération des headers
		$TabHeader = $this->_TabGetHeader($sTokenSession);

		//Disconnect
		$clReponseXML = $OnlineProxy->disconnect($TabHeader);
		$this->_VarDumpRes('Disconnect', $clReponseXML);
		return $clReponseXML;
	}

	/**
	 * pour tester la connexion/déconnexion
	 * @Route("/connexion/{host}", name="connexion", defaults={"host"="127.0.0.1:8062"})
	 */
	public function connexionAction($host)
	{
		ob_start();

		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	/**
	 * pour tester la connexion/déconnexion
	 * @Route("/cnx_error/{error}/{host}", name="cnx_error", defaults={"host"="127.0.0.1:8062"})
	 */
	public function cnxErrorAction($host, $error)
	{
		ob_start();

		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//GetTokenSession
		$clGetTokenSession = $this->get('nout_online.connection_manager')->getGetTokenSession($error);
		$clReponseXML = $OnlineProxy->getTokenSession($clGetTokenSession);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML);
		$this->_VarDumpRes('GetTokenSession', $clReponseXML->sGetTokenSession());

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	/**
	 * pour tester la connexion/déconnexion
	 * @Route("/cnx_try_error/{error}/{host}", name="cnx_try_error", defaults={"host"="127.0.0.1:8062"})
	 */
	public function cnxTryErrorAction($host, $error)
	{
		ob_start();

		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));
		//GetTokenSession
		$clGetTokenSession = $this->get('nout_online.connection_manager')->getGetTokenSession($error);

		try
		{
			$clReponseXML = $OnlineProxy->getTokenSession($clGetTokenSession);
		}
		catch(\Exception $e)
		{
			//on ne veut pas l'objet retourné par NUSOAP qui est un tableau associatif mais un objet qui permet de manipuler la réponse
			$clReponseXML = $OnlineProxy->getXMLResponseWS();

			//on attrape l'exception
			$this->_VarDumpRes('GetTokenSession', $clReponseXML);
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->bIsFault());
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->getTabError());
		}


		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}

	protected function _SupprAccents($str, $encoding='utf-8')
	{
		// transformer les caractères accentués en entités HTML
		$str = htmlentities($str, ENT_NOQUOTES, $encoding);

		// remplacer les entités HTML pour avoir juste le premier caractères non accentués
		// Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
		$str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

		// Remplacer les ligatures tel que : Œ, Æ ...
		// Exemple "Å“" => "oe"
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
		// Supprimer tout le reste
		$str = preg_replace('#&[^;]+;#', '', $str);

		return $str;
	}

	protected function _bEstNumerique($form)
	{
		return strlen(str_replace(array(0,1,2,3,4,5,6,7,8,9), array('', '', '', '', '', '', '', '', '', ''), $form))==0;
	}

	protected function _sNettoieForm($form)
	{
		// prefixe pour les balises où on utilise l'identifiant au lieu du libellé
		$_pszPrefixeBaliseID = "id_";

		if ($this->_bEstNumerique($form))
			return $_pszPrefixeBaliseID.$form;


		// caractères interdits dans une balise
		$_pszCaractereInterdit = " ()[]<>':/!;\"%$&@*°";
		// caractères interdits en début de balise
		$_pszCaractereInterditDebut = "0123456789.-";
		// prefixe interdit en début de balise
		$_pszChaineInterditDebut = "xml";

		//---------------------------------------------------------------------
		/*
		XML elements must follow these naming rules:
		- Names can contain letters, numbers, and other characters..............OK
		- Names must not start with a number or punctuation character...........OK
		- Names must not start with the letters xml (or XML, or Xml, etc).......OK
		- Names cannot contain spaces...........................................OK
		*/

		// supprimer les espaces en début et fin de chaine
		$form = trim($form, ' ');

		// supprimer la casse (mettre en minuscule) et les accents
		$form = strtolower($this->_SupprAccents($form));

		// remplacer les espaces et les caractères spéciaux par des '_'
		$nLength=strlen($form)-1;
		while(($nLength>=0) && (strchr($_pszCaractereInterdit, $form[$nLength])!=NULL))
		{
			$pszLibelle[$nLength]=0;
			$nLength--;
		}
		while($nLength>=0)
		{
			if (strchr($_pszCaractereInterdit, $form[$nLength])!=NULL)
				$form[$nLength]='_';
			$nLength--;
		}

		// supprimer tout caractere different d'un alpha en debut de chaine
		$nLength = strlen($form);
		$nIndex = 0;
		while( ($nIndex < $nLength) && !(( ($form[0] >='a') && ($form[0] <='z') ) || ( ($form[0] >='A') && ($form[0] <='Z')) ))
		{
			$form = substr($form, 1);
			$nIndex++;
		}

		// un nom XML ne peut pas commencer par la chaine de caractere "xml"
		while (strncmp($form, $_pszChaineInterditDebut, strlen($_pszChaineInterditDebut)) == 0)
			$form = substr($form, strlen($_pszChaineInterditDebut));

		// si le libelle est une chaine vide, renvoyer faux
		// ce sera l'ID de la colonne qui sera utilisé pour construire le nom de la balise
		if (strlen(trim($form))==0)
			return false;


		// créer un PXSTR qu'il faudra copier dans pszLibelle puis libérer
		return $form;
	}

	protected function _sList(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamList = new ListParams();
		$clParamList->Table = $form;
		$clReponseXML = $OnlineProxy->listAction($clParamList, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('List', $clReponseXML);


		return $clReponseXML;
	}

	protected function _sGetCaculation(OnlineServiceProxy $OnlineProxy, $sTokenSession, $sIDActionContexte, $TabIDColonne)
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

		$clReponseXML = $OnlineProxy->getCalculation($clParamGetCalculation, $this->_TabGetHeader($sTokenSession, $sIDActionContexte));
		$this->_VarDumpRes('GetCalculation', $clReponseXML);
		return $clReponseXML;
	}

	/**
	 * @Route("/list/{form}/{host}", name="list", defaults={"host"="127.0.0.1:8062"})
	 */
	public function listAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWSList = $this->_sList($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList->sGetReturnType(), $clReponseWSList->getNodeXML(), $clReponseWSList->getNodeSchema());

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);

		$clReponseWSCalcul = $this->_sGetCaculation($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDColonne);
		$clReponseWSParser->InitFromXmlXsd($clReponseWSCalcul->sGetReturnType(), $clReponseWSCalcul->getNodeXML(), $clReponseWSCalcul->getNodeSchema());

		$this->_VarDumpRes('Calculation', $clReponseWSParser->m_MapColonne2Calcul);

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	protected function _sExecute(OnlineServiceProxy $OnlineProxy, $sTokenSession, $action)
	{
		$clParamExecute = new Execute();

		if ($this->_bEstNumerique($action))
			$clParamExecute->ID = $action;
		else
			$clParamExecute->Sentence=$action;

		$clReponseXML = $OnlineProxy->execute($clParamExecute, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Execute', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/execute/{action}/{host}", name="execute", defaults={"host"="127.0.0.1:8062"})
	 */
	public function executeAction($action, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS = $this->_sExecute($OnlineProxy, $sTokenSession, $action);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sDrillthrought(OnlineServiceProxy $OnlineProxy, $sTokenSession, $sActionContexte, $colonne, $enreg)
	{
		$clParamDrillThrough = new DrillThrough();
		$clParamDrillThrough->Record = $enreg;
		$clParamDrillThrough->Column = $colonne;

		$clReponseXML = $OnlineProxy->drillThrough($clParamDrillThrough, $this->_TabGetHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('DrillThrough', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/drillthrought/{host}", name="drillthrought", defaults={"host"="127.0.0.1:8062"})
	 */
	public function drillthroughtAction($host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);


		//execute
		$clReponseWSList = $this->_sExecute($OnlineProxy, $sTokenSession, 'Afficher Nb Jour d\'absence par contact');
		$sActionContexte = $clReponseWSList->sGetActionContext();

		//on parse le résultat
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWSList->sGetReturnType(), $clReponseWSList->getNodeXML(), $clReponseWSList->getNodeSchema());

		$StructForm = $clReponseWSParser->clGetStructureElement($clReponseWSList->clGetForm()->getID());
		$TabIDColonne = array_keys($StructForm->m_MapIDColonne2StructColonne);
		$TabIDEnreg = $clReponseWSParser->GetTabIDEnregFromForm($clReponseWSList->clGetForm()->getID());

		$clReponseWSDrill = $this->_sDrillthrought($OnlineProxy, $sTokenSession, $sActionContexte, $TabIDColonne[0], $TabIDEnreg[0]);
		$sActionContexteDrill = $clReponseWSDrill->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		if ($sActionContexte != $sActionContexteDrill)
			$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexteDrill);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sRequest(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $colonne, $valeur)
	{
		$clParamRequest = new Request();
		$clParamRequest->Table = $form;
		$clParamRequest->CondList = "<Condition><CondCol>$colonne</CondCol><CondType>Equal</CondType><CondValue>$valeur</CondValue></Condition>";


		$clReponseXML = $OnlineProxy->request($clParamRequest, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Request', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/request/{form}/{colonne}/{valeur}/{host}", name="request", defaults={"host"="127.0.0.1:8062"})
	 */
	public function requestAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS = $this->_sRequest($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	protected function _sSearch(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamSearch = new Search();
		$clParamSearch->Table = $form;
		$clReponseXML = $OnlineProxy->search($clParamSearch, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Search', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/search/{form}/{host}", name="search", defaults={"host"="127.0.0.1:8062"})
	 */
	public function searchAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la recherche
		$clReponseWS=$this->_sSearch($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sDisplay(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDisplay = new Display();
		$clParamDisplay->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamDisplay->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->display($clParamDisplay, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Display', $clReponseXML);


		return $clReponseXML;
	}


	/**
	 * @Route("/display/{form}/{id}/{host}", name="display", defaults={"host"="127.0.0.1:8062"})
	 */
	public function displayAction($form, $id, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS=$this->_sDisplay($OnlineProxy, $sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	protected function _sGetColInRecord(OnlineServiceProxy $OnlineProxy, $sTokenSession, $colonne, $id, $content)
	{
		$clParamGCR = new GetColInRecord();
		$clParamGCR->Column = $colonne;
		$clParamGCR->Record = $id;
		$clParamGCR->WantContent=$content;


		$clReponseXML = $OnlineProxy->getColInRecord($clParamGCR, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('GetColInRecord', $clReponseXML);


		return $clReponseXML;
	}


	/**
	 * @Route("/getcolinrecord/{colonne}/{id}/{content}/{host}", name="getcolinrecord", defaults={"host"="127.0.0.1:8062"})
	 */
	public function getColInRecordAction($colonne, $id, $host, $content)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$clReponseWS=$this->_sGetColInRecord($OnlineProxy, $sTokenSession, $colonne, $id, $content);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 */
	protected function _Validate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $nIDContexteAction)
	{
		$clReponseWS = $OnlineProxy->validate($this->_TabGetHeader($sTokenSession, $nIDContexteAction));

		$this->_VarDumpRes('Validate', $clReponseWS);

		return $clReponseWS;
	}


	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 */
	protected function _Cancel(OnlineServiceProxy $OnlineProxy, $sTokenSession, $nIDContexteAction)
	{
		$clParamCancel = new Cancel();
		$clParamCancel->ByUser = 1;
		$clParamCancel->Context = 0;

		$clReponseWS = $OnlineProxy->cancel($clParamCancel,$this->_TabGetHeader($sTokenSession, $nIDContexteAction));

		$this->_VarDumpRes('Cancel', $clReponseWS);

		return $clReponseWS;
	}


	protected function _sModify(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamModify = new Modify();
		$clParamModify->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamModify->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->modify($clParamModify, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Modify', $clReponseXML);


		return $clReponseXML;
	}


	protected function _sUpdate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $nIDContexteAction, $form, $id, $colonne, $valeur)
	{
		$clParamUpdate = new Update();
		$clParamUpdate->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamUpdate->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$baliseColonne = $this->_sNettoieForm($colonne);
		$clParamUpdate->UpdateData = "<xml><$baliseXML id=\"$id\"><$baliseColonne>".htmlentities($valeur)."</$baliseColonne></$baliseXML></xml>";

		$clReponseXML = $OnlineProxy->update($clParamUpdate, $this->_TabGetHeader($sTokenSession, $nIDContexteAction));
		$this->_VarDumpRes('Update', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/modify/{form}/{id}/{colonne}/{valeur}/{host}", name="modify", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /modify/41296233836619/219237638150324/45208949043557/deux
	 */
	public function modifyAction($form, $id, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le modify
		$clReponseWS = $this->_sModify($OnlineProxy, $sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS->sGetReturnType(), $clReponseWS->getNodeXML(), $clReponseWS->getNodeSchema());


		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if (!is_null($clRecord))
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $id, $colonne, $valeur);

			//on valide
			$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);
		}
		else
		{
			echo '<p>On n\'a pas l\'enregistrement</p>';
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function __sCreate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamCreate = new Create();
		$clParamCreate->Table = $form;

		$clReponseXML = $OnlineProxy->create($clParamCreate, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Create', $clReponseXML);

		return $clReponseXML;
	}

	protected function _sCreate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $colonne, $valeur)
	{
		//ici il faut faire le modify
		$clReponseWS = $this->__sCreate($OnlineProxy, $sTokenSession, $form);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on parse le XML pour avoir les enregistrement
		$clReponseWSParser = new ReponseWSParser();
		$clReponseWSParser->InitFromXmlXsd($clReponseWS->sGetReturnType(), $clReponseWS->getNodeXML(), $clReponseWS->getNodeSchema());

		$clRecord = $clReponseWSParser->clGetRecord($clReponseWS->clGetForm(), $clReponseWS->clGetElement());
		if (!is_null($clRecord))
		{
			//on met à jour la valeur de la colonne
			$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $clReponseWS->clGetElement()->getID(), $colonne, $valeur);

			//on valide
			$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);
			return $clRecord->m_nIDEnreg;
		}

		echo '<p>On n\'a pas l\'enregistrement</p>';
		return null;
	}

	/**
	 * @Route("/create/{form}/{colonne}/{valeur}/{host}", name="create", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /create/41296233836619/45208949043557/trois
	 */
	public function createAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$this->_sCreate($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}

	/**
	 * @Route("/select_form/{form}/{host}", name="select_form", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /selectForm/48918773563102
	 */
	public function selectFormAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$this->__sCreate($OnlineProxy, $sTokenSession, $form);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sCreateFrom(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $origine)
	{
		$clParamCreateFrom = new CreateFrom();
		$clParamCreateFrom->Table = $form;
		$clParamCreateFrom->TableSrc = $form;
		$clParamCreateFrom->ElemSrc = $origine;

		$clReponseXML = $OnlineProxy->createFrom($clParamCreateFrom, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('CreateFrom', $clReponseXML);

		return $clReponseXML;
	}

	/**
	 * @Route("/create_from/{form}/{origine}/{host}", name="create_from", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /create/41296233836619/45354977933184
	 *
	 */
	public function createFromAction($form, $origine, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le modify
		$clReponseWS = $this->_sCreateFrom($OnlineProxy, $sTokenSession, $form, $origine);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide
		$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	protected function _sDelete(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDelete = new Delete();
		$clParamDelete->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamDelete->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->delete($clParamDelete, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Delete', $clReponseXML);


		return $clReponseXML;
	}

	protected function _sConfirmResponse(OnlineServiceProxy $OnlineProxy, $sTokenSession, $sActionContexte, MessageBox $clMessageBox)
	{
		$clConfirm = new ConfirmResponse();
		$clConfirm->TypeConfirmation = array_key_exists(MessageBox::IDYES, $clMessageBox->m_TabButton) ? MessageBox::IDYES : MessageBox::IDOK;

		$clReponseWS = $OnlineProxy->ConfirmResponse($clConfirm, $this->_TabGetHeader($sTokenSession, $sActionContexte));
		$this->_VarDumpRes('ConfirmResponse', $clReponseWS);

		return $clReponseWS;
	}

	/**
	 * @Route("/delete/{form}/{colonne}/{valeur}/{host}", name="delete", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /delete
	 */
	public function deleteAction($form, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		$sIDEnreg = $this->_sCreate($OnlineProxy, $sTokenSession, $form, $colonne, $valeur);

		//ici il faut faire le delete
		$clReponseWS = $this->_sDelete($OnlineProxy, $sTokenSession, $form, $sIDEnreg);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on valide
		if ($clReponseWS->sGetReturnType()==XMLResponseWS::RETURNTYPE_MESSAGEBOX)
		{
			//il faut confirmer la réponse
			$this->_sConfirmResponse($OnlineProxy, $sTokenSession, $sActionContexte, $clReponseWS->clGetMessageBox());
		}

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sGetStartAutomatism(OnlineServiceProxy $OnlineProxy, $sTokenSession)
	{
		$clParamStartAutomatism = new GetStartAutomatism();

		$clReponseXML = $OnlineProxy->getStartAutomatism($clParamStartAutomatism, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('GetStartAutomatism', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/getstartautomatism/{host}", name="getstartautomatism", defaults={"host"="127.0.0.1:8062"})
	 */
	public function getStartAutomatismAction($host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS = $this->_sGetStartAutomatism($OnlineProxy, $sTokenSession);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	protected function _sGetEndAutomatism(OnlineServiceProxy $OnlineProxy, $sTokenSession)
	{
		$clParamEndAutomatism = new GetEndAutomatism();

		$clReponseXML = $OnlineProxy->getEndAutomatism($clParamEndAutomatism, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('GetEndAutomatism', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/getendautomatism/{host}", name="getendautomatism", defaults={"host"="127.0.0.1:8062"})
	 */
	public function getEndAutomatismAction($host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//la liste
		$clReponseWS = $this->_sGetEndAutomatism($OnlineProxy, $sTokenSession);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//annulation de la liste
		$this->_Cancel($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}





	/**
	 * @Route("/record_test", name="record_test")
	 *
	 */
	public function recordTestAction()
	{
		ob_start();

		$sXML = file_get_contents('./bundles/noutonline/test/xml/FormEtatChamp_fiche_listesync.xml');
		$clResponseXML = new XMLResponseWS($sXML);

		$clRecordManager = new ReponseWSParser();

		$clRecordManager->InitFromXmlXsd($clResponseXML->sGetReturnType(), $clResponseXML->getNodeXML(), $clResponseXML->getNodeSchema());


		var_dump($clRecordManager);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


	/**
	 * @Route("/filenpi_test", name="filenpi_test")
	 *
	 */
	public function filenpiTestAction()
	{
		ob_start();

		$clFileNPI = new ConditionFileNPI();

		/*                                          (                                   <Operator type="ET">
		 * (                                            ET,                                 <Operator type="ET">
		 *      (                                       (                                       <Condition> .. M .. </Condition>
		 *           (                                      ET,                                     <Operator type="ET">
		 *               (A, B, ET),                        M,                                          <Operator type="ET">
		 *               (C, NOT),                          (                                               <Condition> .. L .. </Condition>
		 *               OU                                     ET,                                         <Condition> .. K .. </Condition>
		 *           ),                                         (ET, K, L),                             </Operator>
		 *           NOT                                        (ET, J, I),                             <Operator type="ET">
		 *      ),                                          )                                               <Condition> .. J .. </Condition>
		 *      (                                       ),                                                  <Condition> .. I .. </Condition>
		 *           D,                                 (                                           </Operator>
		 *           (                     =>               OU,                                 </Operator>
		 *               (E, F, OU),                        (                               </Operator>
		 *               (G, H, ET),                            OU,                         <Operator type="OU">
		 *                ET                                    (                               <Operator type="OU">
		 *           ),                                             ET,                             <Operator type="ET">
		 *           OU                                             (ET, H, G),                         <Operator type="ET">
		 *      ),                                                  (OU, F, E),                             <Condition> .. H .. </Condition>
		 *      OU                                               ),                                         <Condition> .. G .. </Condition>
		 * ),                                                    D                                      </Operator>
		 * (                                                ),                                          <Operator type="OU">
		 *      (                                           (                                               <Condition> .. F .. </Condition>
		 *          (I, J, ET),                                 NOT,                                        <Condition> .. E .. </Condition>
		 *          (K, L, ET),                                 (                                       </Operator>
		 *          ET                                              OU,                             </Operator>
		 *      ),                                                  (                               <Condition> .. D .. </Condition>
		 *      M,                                                      (NOT, C),               </Operator>
		 *      ET                                                      (ET, B, A)              <Operator type="NOT">
		 * ),                                                       )                               <Operator type="OU">
		 * ET                                                   )                                       <Operator type="NOT">
		 *                                                  )                                               <Condition> .. C .. </Condition>
		 *                                              )                                               </Operator>
		 *                                          )                                               <Operator type="ET">
		 *                                                                                              <Condition> .. B .. </Condition>
		 *                                                                                              <Condition> .. B .. </Condition>
		 *                                                                                          </Operator>
		 *                                                                                      </Operator>
		 *                                                                                  </Operator>
		 *                                                                              </Operator>
		 *
		 * => A, B, ET, C, NOT, OU, NOT, D, E, F, OU, G, H, ET, ET, OU, OU, I, J, ET, K, L, ET, ET, M, ET, ET
		 */

		$sResultatAttendu = <<<RESULTAT
<Operator type="AND">
	<Operator type="AND">
		<Condition><CondCol>M</CondCol><CondType>DoNotContain</CondType><CondValue>a</CondValue></Condition>
			<Operator type="AND">
				<Operator type="AND">
					<Condition><CondCol>L</CondCol><CondType>DoNotEndWith</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>K</CondCol><CondType>DoNotBeginWith</CondType><CondValue>a</CondValue></Condition>
				</Operator>
				<Operator type="AND">
					<Condition><CondCol>J</CondCol><CondType>Contain</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>I</CondCol><CondType>BeginWith</CondType><CondValue>a</CondValue></Condition>
			</Operator>
		</Operator>
	</Operator>
	<Operator type="OR">
		<Operator type="OR">
			<Operator type="AND">
				<Operator type="AND">
					<Condition><CondCol>H</CondCol><CondType>BeginWithWordByWord</CondType><CondValue>a</CondValue></Condition>
					<Condition><CondCol>G</CondCol><CondType>WithRight</CondType><CondValue>1</CondValue></Condition>
				</Operator>
				<Operator type="OR">
					<Condition><CondCol>F</CondCol><CondType>BetterOrEqual</CondType><CondValue>1</CondValue></Condition>
					<Condition><CondCol>E</CondCol><CondType>Better</CondType><CondValue>1</CondValue></Condition>
				</Operator>
			</Operator>
			<Condition><CondCol>D</CondCol><CondType>LessOrEqual</CondType><CondValue>1</CondValue></Condition>
		</Operator>
		<Operator type="NOT">
			<Operator type="OR">
				<Operator type="NOT">
					<Condition><CondCol>C</CondCol><CondType>Less</CondType><CondValue>1</CondValue></Condition>
				</Operator>
				<Operator type="AND">
					<Condition><CondCol>B</CondCol><CondType>Different</CondType><CondValue>1</CondValue></Condition>
					<Condition><CondCol>A</CondCol><CondType>Equal</CondType><CondValue>1</CondValue></Condition>
				</Operator>
			</Operator>
		</Operator>
	</Operator>
</Operator>
RESULTAT;

		//=> A, B, ET, C, NOT, OU, NOT, D, E, F, OU, G, H, ET, ET, OU, OU, I, J, ET, K, L, ET, ET, M, ET, ET

		$clFileNPI->EmpileCondition('A', ConditionColonne::COND_EQUAL, '1');
		$clFileNPI->EmpileCondition('B', ConditionColonne::COND_DIFFERENT, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('C', ConditionColonne::COND_LESS, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_NOT);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_NOT);
		$clFileNPI->EmpileCondition('D', ConditionColonne::COND_LESSOREQUAL, '1');
		$clFileNPI->EmpileCondition('E', ConditionColonne::COND_BETTER, '1');
		$clFileNPI->EmpileCondition('F', ConditionColonne::COND_BETTEROREQUAL, '1');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileCondition('G', ConditionColonne::COND_WITHRIGHT, '1');
		$clFileNPI->EmpileCondition('H', ConditionColonne::COND_BEGINWITHWORDBYWORD, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_OR);
		$clFileNPI->EmpileCondition('I', ConditionColonne::COND_BEGINWITH, 'a');
		$clFileNPI->EmpileCondition('J', ConditionColonne::COND_CONTAIN, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('K', ConditionColonne::COND_DONOTBEGINWITH, 'a');
		$clFileNPI->EmpileCondition('L', ConditionColonne::COND_DONOTENDWITH, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileCondition('M', ConditionColonne::COND_DONOTCONTAIN, 'a');
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);
		$clFileNPI->EmpileOperateur(ConditionOperateur::OP_AND);

		$sSoap = $clFileNPI->sToSoap();

		//$this->assertEquals(str_replace(array("\t", "\n", "\r"), array("","",""), $sResultatAttendu), $sSoap);

		var_dump($sSoap);
		var_dump(str_replace(array("\t", "\n", "\r"), array("","",""), $sResultatAttendu));
		var_dump(str_replace(array("\t", "\n", "\r"), array("","",""), $sResultatAttendu)==$sSoap);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}
}