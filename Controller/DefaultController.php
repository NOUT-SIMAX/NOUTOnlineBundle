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
    //TODO: controlleur trop volumineux, sortir le code metier

	/**
	 * @return OnlineServiceProxy
	 */
	protected function _clGetOnlineProxy()
	{
		$clConfiguration = $this->get('nout_online.configuration_dialogue');
		return $this->get('nout_online.service_factory')->clGetSOAPProxy($clConfiguration);
	}

	/**
	 * fonction permettant de generer les info de connexion pour recuperer le token de session
	 *
	 * @param string $sLogin login (default:emtpy)
	 * @param string $sPass (default:emtpy)
	 * @return object instance of GetTokenSession
	 */
	private function __clGetTokenSessionParams($sLogin ='', $sPass = '')
	{
		$clTokenSession = new GetTokenSession();
		$clTokenSession->UsernameToken = new UsernameToken($sLogin, $sPass);

		//TODO : gestion langue gestion extranet
		$clTokenSession->DefaultClientLanguageCode=12;

		return $clTokenSession;
	}
	//--------


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

	    /*
	    $request = $this->get('request');
	    $session = $request->getSession();

	    // get the login error if there is one
	    if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
	    {
		    $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
	    }
	    else
	    {
		    $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
		    $session->remove(SecurityContext::AUTHENTICATION_ERROR);
	    }

	    /*
	     return $this->render('AcmeSecurityBundle:Security:login.html.twig', array(
            // last username entered by the user
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error'         => $error,
        ));
	     */

	    //return $this->_renderConnectForm(null);




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


    /**
     * fonction qui permet d'executer la tentative de connexion
     */
    private function __tryConnect()
    {
	    //recuperation du proxy simaxOnline
	    $OnlineProxy = $this->_clGetOnlineProxy();

        $oRequest = $this->get('request')/*->request*/; //->request pour uniquement les valeur post
	    $aForm = $oRequest->get('form');

        //execution requete de connexion
        try
        {
            $clReponseXML = $OnlineProxy->getTokenSession($this->__clGetTokenSessionParams($aForm['m_sLogin'], $aForm['m_sPass']));
        }
        catch(SOAPException $clSoapEx)
        {
            //on retourne a la page d'origine en fournissant message d'erreur et code erreur

            //TODO: return le form avec le message d'erreur
	        $clReponseXML = $OnlineProxy->getXMLResponseWS();
	        return $this->_renderConnectForm($clReponseXML->getMessError());
        }


	    return $this->_renderConnectForm('connexion OK');
/*
       if( $this->__bCheckConnection($clReponseXML))
       {
           //la connexion reussis on redirige vers un nouveau controlleur
       }
       else
       {
           //echec connexion, on reaffiche le formulaire de connexion
           //comment
       }*/
    }
    //-------


    private function __fGenerateLogForm()
    {

    }
    //-----

    private function __bCheckConnection(XMLResponseWS $clResponseXml)
    {
//        $clXmlRespParser = new XMLResponseWS($clResponseXml->sGetXML());

//        var_dump($clXmlRespParser);

    }
    //---------




}
