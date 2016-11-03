<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;


/**
 * Class DefaultController
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 */
class DefaultController extends Controller
{
	/**
	 * @Route("/", name="online_index")
	 */
    public function indexAction()
    {
    	return $this->render('NOUTOnlineBundle:Default:index.html.twig');
    }

    /**
     * @Route("/test", name="online_test")
     */
    public function testAction(Request $request)
    {
        ob_start();

        //var_dump($var);
        //$request = Request::createFromGlobals();

        // the URI being requested (e.g. /about) minus any query parameters
        var_dump($request->getPathInfo());


        // retrieve $_SERVER variables
        var_dump($request->server->get('HTTP_HOST'));

        // /blog/my-blog-post
        var_dump($this->generateUrl('index',array(), UrlGeneratorInterface::ABSOLUTE_URL));

        var_dump($_SERVER);




        $containt = ob_get_contents();
        ob_get_clean();

        return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
    }

}
