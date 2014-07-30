<?php

namespace NOUT\Bundle\NOUTOnlineBundle\Controller;

// this imports the annotations
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ListParams;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Modify;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\Update;
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

	protected function _TabGetHeader($sTokenSession, $nIDContexteAction=null)
	{
		$clUsernameToken = $this->get('nout_online.connection_manager')->getUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession);

		if (isset($nIDContexteAction))
			$TabHeader['ActionContext']=$nIDContexteAction;

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


	/**
	 * pour tester la connexion/déconnexion
	 * @Route("/cnx_try_error/{error}/{host}", name="cnx_try_error", defaults={"host"="127.0.0.1:8062"})
	 */
	public function cnxTryErrorAction($host, $error)
	{
		ob_start();

		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));
		//GetTokenSession
		$clGetTokenSession = $this->get('nout_online.connection_manager')->getGetTokenSession($error);

		try
		{
			$clReponseXML = $OnlineProxy->getTokenSession($clGetTokenSession);
		}
		catch(\Exception $e)
		{
			//on ne veut pas l'objet retourné par NUSOAP qui est un tableau associatif mais un objet qui permet de manipuler la réponse
			$clReponseXML = $OnlineProxy->getXMLResponseWS();

			//on attrape l'exception
			$this->_VarDumpRes('GetTokenSession', $clReponseXML);
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->bIsFault());
			$this->_VarDumpRes('GetTokenSession', $clReponseXML->getTabError());
		}


		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}

	protected function _SupprAccents($str, $encoding='utf-8')
	{
		// transformer les caractères accentués en entités HTML
		$str = htmlentities($str, ENT_NOQUOTES, $encoding);

		// remplacer les entités HTML pour avoir juste le premier caractères non accentués
		// Exemple : "&ecute;" => "e", "&Ecute;" => "E", "Ã " => "a" ...
		$str = preg_replace('#&([A-za-z])(?:acute|grave|cedil|circ|orn|ring|slash|th|tilde|uml);#', '\1', $str);

		// Remplacer les ligatures tel que : Œ, Æ ...
		// Exemple "Å“" => "oe"
		$str = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $str);
		// Supprimer tout le reste
		$str = preg_replace('#&[^;]+;#', '', $str);

		return $str;
	}


	protected function _sNettoieForm($form)
	{
		// prefixe pour les balises où on utilise l'identifiant au lieu du libellé
		$_pszPrefixeBaliseID = "id_";

		if (strlen(str_replace(array(0,1,2,3,4,5,6,7,8,9), array('', '', '', '', '', '', '', '', '', ''), $form))==0)
			return $_pszPrefixeBaliseID.$form;


		// caractères interdits dans une balise
		$_pszCaractereInterdit = " ()[]<>':/!;\"%$&@*°";
		// caractères interdits en début de balise
		$_pszCaractereInterditDebut = "0123456789.-";
		// prefixe interdit en début de balise
		$_pszChaineInterditDebut = "xml";

		//---------------------------------------------------------------------
		/*
		XML elements must follow these naming rules:
		- Names can contain letters, numbers, and other characters..............OK
		- Names must not start with a number or punctuation character...........OK
		- Names must not start with the letters xml (or XML, or Xml, etc).......OK
		- Names cannot contain spaces...........................................OK
		*/

		// supprimer les espaces en début et fin de chaine
		$form = trim($form, ' ');

		// supprimer la casse (mettre en minuscule) et les accents
		$form = strtolower($this->_SupprAccents($form));

		// remplacer les espaces et les caractères spéciaux par des '_'
		$nLength=strlen($form)-1;
		while(($nLength>=0) && (strchr($_pszCaractereInterdit, $form[$nLength])!=NULL))
		{
			$pszLibelle[$nLength]=0;
			$nLength--;
		}
		while($nLength>=0)
		{
			if (strchr($_pszCaractereInterdit, $form[$nLength])!=NULL)
				$form[$nLength]='_';
			$nLength--;
		}

		// supprimer tout caractere different d'un alpha en debut de chaine
		$nLength = strlen($form);
		$nIndex = 0;
		while( ($nIndex < $nLength) && !(( ($form[0] >='a') && ($form[0] <='z') ) || ( ($form[0] >='A') && ($form[0] <='Z')) ))
		{
			$form = substr($form, 1);
			$nIndex++;
		}

		// un nom XML ne peut pas commencer par la chaine de caractere "xml"
		while (strncmp($form, $_pszChaineInterditDebut, strlen($_pszChaineInterditDebut)) == 0)
			$form = substr($form, strlen($_pszChaineInterditDebut));

		// si le libelle est une chaine vide, renvoyer faux
		// ce sera l'ID de la colonne qui sera utilisé pour construire le nom de la balise
		if (strlen(trim($form))==0)
			return false;


		// créer un PXSTR qu'il faudra copier dans pszLibelle puis libérer
		return $form;
	}

	protected function _sList(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form)
	{
		$clParamList = new ListParams();
		$clParamList->Table = $form;
		$clReponseXML = $OnlineProxy->listAction($clParamList, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('List', $clReponseXML);


		return $clReponseXML;
	}

	/**
	 * @Route("/list/{form}/{host}", name="list", defaults={"host"="127.0.0.1:8062"})
	 */
	public function listAction($form, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le display
		$this->_sList($OnlineProxy, $sTokenSession, $form);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}



	protected function _sDisplay(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamDisplay = new Display();
		$clParamDisplay->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamDisplay->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

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


	/**
	 * Valide la dernière action du contexte
	 * @param $sTokenSession
	 * @param $nIDContexteAction
	 */
	protected function _Validate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $nIDContexteAction)
	{
		$clReponseWS = $OnlineProxy->validate($this->_TabGetHeader($sTokenSession, $nIDContexteAction));
		return $clReponseWS;
	}


	protected function _sModify(OnlineServiceProxy $OnlineProxy, $sTokenSession, $form, $id)
	{
		$clParamModify = new Modify();
		$clParamModify->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamModify->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$clReponseXML = $OnlineProxy->modify($clParamModify, $this->_TabGetHeader($sTokenSession));
		$this->_VarDumpRes('Modify', $clReponseXML);


		return $clReponseXML;
	}


	protected function _sUpdate(OnlineServiceProxy $OnlineProxy, $sTokenSession, $nIDContexteAction, $form, $id, $colonne, $valeur)
	{
		$clParamUpdate = new Update();
		$clParamUpdate->Table = $form;

		$baliseXML = $this->_sNettoieForm($form);
		$clParamUpdate->ParamXML = "<$baliseXML>".htmlentities($id)."</$baliseXML>";

		$baliseColonne = $this->_sNettoieForm($colonne);
		$clParamUpdate->UpdateData = "<xml><$baliseXML id=\"$id\"><$baliseColonne>".htmlentities($valeur)."</$baliseColonne></$baliseXML></xml>";

		$clReponseXML = $OnlineProxy->update($clParamUpdate, $this->_TabGetHeader($sTokenSession, $nIDContexteAction));
		$this->_VarDumpRes('Update', $clReponseXML);

		/*
		 *
		 * <id_1169 simax:id="27" simax:title="automatisme temporel" simax-layout:bold="1" simax-layout:italic="0">
<id_1171>automatisme temporel</id_1171>
<id_1172>automatisme</id_1172>
<id_1173>temporel</id_1173>
<id_1174/>
<id_7623/>
<id_7624>1</id_7624>
<id_10545>0</id_10545>
</id_1169>
		 */

		return $clReponseXML;
	}

	/**
	 * @Route("/modify/{form}/{id}/{colonne}/{valeur}/{host}", name="modify", defaults={"host"="127.0.0.1:8062"})
	 *
	 * exemple GUID : /modify/41296233836619/219237638150324/45208949043557/deux
	 */
	public function modifyAction($form, $id, $colonne, $valeur, $host)
	{
		ob_start();
		$OnlineProxy = $this->get('nout_online.service_factory')->clGetServiceProxy($this->_clGetConfiguration($host));

		//la connexion
		$sTokenSession = $this->_sConnexion($OnlineProxy);

		//ici il faut faire le modify
		$clReponseWS = $this->_sModify($OnlineProxy, $sTokenSession, $form, $id);
		$sActionContexte = $clReponseWS->sGetActionContext();

		//on met à jour la valeur de la colonne
		$this->_sUpdate($OnlineProxy, $sTokenSession, $sActionContexte, $form, $id, $colonne, $valeur);

		//on valide
		$this->_Validate($OnlineProxy, $sTokenSession, $sActionContexte);

		//la deconnexion
		$this->_bDeconnexion($OnlineProxy, $sTokenSession);

		$containt = ob_get_contents();
		ob_get_clean();
		return $this->render('NOUTOnlineBundle:Default:debug.html.twig', array('containt'=>$containt));
	}


}