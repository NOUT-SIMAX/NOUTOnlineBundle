<?php

namespace NOUT\Bundle\NOUTSessionManagerBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
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
	protected function _renderConnectForm($error)
	{
		//si on est pas connecté on affiche le formulaire de connexion
		$clConnectInfo = new ConnectionInfos();

		$clFormBuilder = $this->get('form.factory')->createBuilder('form', $clConnectInfo);

		$clFormBuilder
			->add('m_sLogin', 'text', array('label' => 'nom d\'utilisateur'))
			->add('m_sPass', 'password', array('label' => 'mot de passe', 'required' => false))
			->add('connexion', 'submit');

		//generation du formulaire avec le builder
		return $this->render(
			'NOUTSessionManagerBundle:Default:formLogin.html.twig',
			array('form'=> $clFormBuilder->getForm()->createView(), 'error'=>$error)
		);
	}

    /**
     *  Route de génération du formulaire de connexion
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
