<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OptionDialogue;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// this imports the annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;

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

	/**
	 * @Route("/record", name="record")
	 */
	public function recordAction()
	{
		$sXML = file_get_contents('./bundles/noutonline/test/xml/FormEtatChamp_fiche_listesync.xml');
		$clResponseXML = new XMLResponseWS($sXML);

		$clOptionDialogue = new OptionDialogue();
		$clOptionDialogue->DisplayValue = 16638;
		$clOptionDialogue->Readable = 0;
		$clOptionDialogue->EncodingOutput = 0;
		$clOptionDialogue->LanguageCode = 12;
		$clOptionDialogue->WithFieldStateControl = 1;


		$clRecord = new Record(Record::LEVEL_RECORD, $clResponseXML->clGetForm(), $clResponseXML->clGetElement());
		$clRecord->initFromReponseWS($clOptionDialogue, $clResponseXML->getNodeXML('Modify'), $clResponseXML->getNodeSchema());


		$response = new Response(json_encode($clRecord));
		//$response->headers->set('Content-Type', 'application/json');
		return $response;
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

	/**
	 * pour tester la connexion déconnexion
	 * @Route("/connexion/{host}", name="connexion", defaults={"host"="127.0.0.1:8062"})
	 */
	public function connexionAction($host)
	{
		$clServiceFactory = $this->get('nout_online.service_factory');
		$OnlineProxy = $clServiceFactory->clGetServiceProxy($this->_clGetConfiguration($host));

		$clConnectionManager = $this->get('nout_online.connection_manager');

		//GetTokenSession
		$clGetTokenSession = $clConnectionManager->getGetTokenSession();
		$sTokenSession = $OnlineProxy->getTokenSession($clGetTokenSession);

		ob_start();
		echo '<h1>GetTokenSession</h1>';
		var_dump($sTokenSession);

		//Disconnect
		$clUsernameToken = $clConnectionManager->getUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession);
		$bDisconnect = $OnlineProxy->disconnect($TabHeader);

		echo '<h1>Disconnect</h1>';
		var_dump($bDisconnect);

		$containt = ob_get_contents();
		ob_get_clean();


		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}

}