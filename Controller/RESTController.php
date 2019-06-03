<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 05/08/2015
 * Time: 13:39
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;


use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy as RESTProxy;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route; // this imports the annotations

/**
 * Class DefaultController
 * @package NOUT\Bundle\NOUTOnlineBundle\Controller
 * @Route("/rest")
 */
class RESTController extends ProxyController
{
	/**
	 * @param $host
	 * @return RESTProxy
	 */
	protected function _clGetRESTProxy($host)
	{
		return $this->get('nout_online.service_factory')->clGetRESTProxy($this->_clGetConfiguration($host));
	}

	/**
	 * @Route("/test/{host}",
     *     name="online_rest_test",
     *     defaults={"host"=""}
     * )
	 */
	public function restTestAction($host)
	{
		ob_start();

		$OnlineProxy = $this->_clGetRESTProxy($host);
		//$OnlineProxy->bGetUserExists('ninon');

		$sXML = file_get_contents('./bundles/noutonline/test/small-mario.png', null, null, 0, 20);
		echo $sXML;


		$containt = ob_get_contents();
		ob_get_clean();

		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt' => $containt));
	}
}