<?php

namespace NOUT\Bundle\NOUTSessionManagerBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\REST\OnlineServiceProxy;
use NOUT\Bundle\NOUTSessionManagerBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\NOUTSessionManagerBundle\Entity\ConnectionInfos;
use Symfony\Component\Security\Core\SecurityContext;


class DefaultController extends Controller
{

	/**
	 * @return OnlineServiceProxy;
	 */
	protected function _clGetRESTProxy()
	{
		$clServiceFactory = $this->get('nout_online.service_factory');
		$clConfiguration = $this->get('nout_online.configuration_dialogue');

		return $clServiceFactory->clGetRESTProxy($clConfiguration);
	}

    /**
     * Route de génération du formulaire de connexion
     * Comme c'est pour l'identification, on n'utilise pas de formBuilder, uniquement un template twig avec le formulaire dedans
     *
     * @Route("/login/", name="login")
     */
    public function loginAction()
    {
	    $request = $this->get('request');
	    $session = $request->getSession();
	    // get the login error if there is one
	    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
		    $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
	    } else {
		    $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		    $session->remove(SecurityContext::AUTHENTICATION_ERROR);
	    }

	    return $this->render('NOUTSessionManagerBundle:Security:login.html.twig', array(
		    // last username entered by the user
		    'last_username' => $session->get(SecurityContext::LAST_USERNAME),
		    'error'         => $error,
		    'version_noutonline' => $this->_clGetRESTProxy()->sGetVersion(),
	    ));

    }

	/**
	 *  Route de génération du formulaire de connexion
	 *
	 * @Route("/", name="session_index")
	 */
	public function indexAction()
	{
		$oToken = $this->get('security.context')->getToken();
		$oUser = $oToken->getUser();

		//page d'index
		return $this->render(
			'NOUTSessionManagerBundle:Default:index.html.twig',
			array('username'=>$oUser->getUsername(), 'tokensession'=>$oToken->getSessionToken())
		);
	}
}
