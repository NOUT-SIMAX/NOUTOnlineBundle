<?php

namespace NOUT\Bundle\NOUTSessionManagerBundle\Controller;

use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
use NOUT\Bundle\NOUTOnlineBundle\Entity\OASIS\UsernameToken;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\GetTokenSession;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\SOAPException;
use NOUT\Bundle\NOUTSessionManagerBundle\Entity\ConnectionInfos;


class DefaultController extends Controller
{
    //TODO: controlleur trop volumineux, sortir le code metier
    private $m_host = '127.0.0.1:8052';


    /**
     *  Route de génération du formulaire de connexion
     *
     * @Route("/connectForm/", name="connectForm")
     */
    public function connectForm()
    {
        $bConnected = false;

        //si on a soumission du formulaire, on appelle le controleur fait pour cela
        if($this->get('request')->get('m_sLogin'))
        {
           return $this->__tryConnect();
        }

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
                array('form'=> $clFormBuilder->getForm()->createView())
        );
    }
    //-----


    /**
     * fonction qui permet d'executer la tentative de connexion
     */
    private function __tryConnect()
    {
        $oRequest = $this->get('request')/*->request*/; //->request pour uniquement les valeur post

        //recuperation du proxy simaxOnline
        $OnlineProxy =  $this->get('nout_online.service_factory')->clGetServiceProxy($this->__clGetConfiguration($this->m_host));

        //execution requete de connexion
        try
        {
            $clReponseXML = $OnlineProxy->getTokenSession($this->__clGenerateGetTokenSessionParams($oRequest->get('m_sLogin'), $oRequest->get('m_sPass')));
        }
        catch(SOAPException $clSoapEx)
        {
            //on retourne a la page d'origine en fournissant message d'erreur et code erreur

            //TODO: return le form avec le message d'erreur

        }


       if( $this->__bCheckConnection($clReponseXML))
       {
           //la connexion reussis on redirige vers un nouveau controlleur
       }
       else
       {
           //echec connexion, on reaffiche le formulaire de connexion
           //comment
       }
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


    /**
     * fonction permettant de generer les info de connexion pour recuperer le token de session
     *
     * @param string $sLogin login (default:emtpy)
     * @param string $sPass (default:emtpy)
     * @return object instance of GetTokenSession
     */
    private function __clGenerateGetTokenSessionParams($sLogin ='', $sPass = '')
    {
        //generation token d'utilisateur
        $clUserNameToken = new UsernameToken($sLogin, $sPass);

        $clTokenSession = new GetTokenSession();
        $clTokenSession->UsernameToken = $clUserNameToken;

        //TODO : gestion langue gestion extranet
        $clTokenSession->DefaultClientLanguageCode=12;
        //$clTokenSession->ExtranetUser = null;

        return $clTokenSession;
    }
    //--------


    /**
     * fonction utile lors de la phase de developpement/test pour la generationd e la configuration
     * @param $host
     * @return ConfigurationDialogue
     */
    private  function __clGetConfiguration($sHost)
    {
        $sEndPoint = './bundles/noutonline/Service.wsdl';
        $sService = 'http://'.$sHost;

        //on récupére le prefixe (http | https);
        $sProtocolPrefix = substr($sService,0,strpos($sService,'//')+2 );

        list($sHost,$sPort) = explode(':', str_replace($sProtocolPrefix,'',$sService) );

        $clConfiguration = new ConfigurationDialogue($sEndPoint, true, $sHost, $sPort,$sProtocolPrefix);
        return $clConfiguration;
    }
    //--------
}
