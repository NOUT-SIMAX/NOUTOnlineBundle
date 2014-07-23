<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

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


	/**
	 * @Route("/token", name="token")
	 */
	public function tokenAction()
	{
		$clServiceFactory = $this->get('noutonline.onlineservice_factory');
		$OnlineProxy = $clServiceFactory->clGetServiceProxy();

		$clConnectionManager = $this->get('noutonline.connection_manager');

		$clGetTokenSession = $clConnectionManager->getGetTokenSession();

		$ret = $OnlineProxy->getTokenSession($clGetTokenSession);

		ob_start();
		var_dump($ret);
		$containt = ob_get_contents();
		ob_get_clean();

		//$response->headers->set('Content-Type', 'application/json');
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}

	/**
	 * @Route("/disconnect/{token}", name="disconnect")
	 */
	public function disconnectAction($token)
	{
		$clServiceFactory = $this->get('noutonline.onlineservice_factory');
		$OnlineProxy = $clServiceFactory->clGetServiceProxy();

		$clConnectionManager = $this->get('noutonline.connection_manager');

		$clUsernameToken = $clConnectionManager->getUsernameToken();

		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$token);

		$ret = $OnlineProxy->disconnect($TabHeader);

		ob_start();
		var_dump($ret);
		$containt = ob_get_contents();
		ob_get_clean();

		//$response->headers->set('Content-Type', 'application/json');
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}






}
