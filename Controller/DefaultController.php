<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

// this imports the annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Display;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\XMLResponseWS;

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

	protected function _TabGetHeader($sTokenSession)
	{
		$clUsernameToken = $this->get('nout_online.connection_manager')->getUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession);

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


	protected function _sDisplay(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDisplay = new Display();
		$clParamDisplay->Table = $form;
		$clParamDisplay->ParamXML = '<'.$form.'>'.$id.'</'.$form.'>';
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
		$this->_sDisplay($OnlineProxy, $sTokenSession, $form, $id);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



}