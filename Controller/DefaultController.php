<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

// this imports the annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

use Symfony\Component\HttpFoundation\Response;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

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
		$clRecord = new Record();
		$clRecord->testXML();

		$response = new Response(json_encode($clRecord));
		$response->headers->set('Content-Type', 'application/json');
		return $response;
	}
}
