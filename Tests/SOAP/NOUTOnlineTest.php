<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\OASIS\UsernameToken;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\OnlineServiceProxy;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ExtranetUserType;

/**
 * Class NOUTOnlineTest
 * classe pour tester NOUTOnline en mode Intranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 */
class NOUTOnlineTest extends \PHPUnit_Framework_TestCase
{
	protected $m_clConfig;
	protected $m_clNOUTOnline;

	public function __construct()
	{
		//on instancie la configuration de NOUTOnline
		$sService = 'http://12.7.0.0.1:8062';
		//on récupére le prefixe (http | https);
		$sProtocolPrefix = substr($sService,0,strpos($sService,'//')+2 );
		list($sHost,$sPort) = explode(':', str_replace($sProtocolPrefix,'',$sService) );

		//il faut récupérer la wsdl depuis le service
		//adresse de la wsdl :  /getwsdl?
		$sHttpWSDL = $sService.'/GetWSDL?';
		$sWSDL = file_get_contents($sHttpWSDL);

		$sEndPoint = './Service.wsdl';
		file_put_contents($sEndPoint, $sWSDL);

		$this->m_clConfig = new ConfigurationDialogue($sEndPoint, true, $sHost, $sPort,$sProtocolPrefix);

		//ici on instancie NOUTOnline
		$this->m_clNOUTOnline = new OnlineServiceProxy($this->m_clConfig, null);
	}

	/**
	 * renvoi le username token avec les bonnes infomations pour la connexion
	 * @return UserNameToken
	 */
	protected function _clGetUsernameToken()
	{
		return new UsernameToken('superviseur', '');
	}

	/**
	 * Génère les paramètres pour la méthode GetTokenSession
	 * @return GetTokenSession
	 */
	protected function _getGetTokenSession($UsernameToken, $UserExtranet=null, $FormExtranet=null)
	{
		//il faut retourner les paramètres pour la connexion
		$clGetTokenSession = new GetTokenSession();
		$clGetTokenSession->DefaultClientLanguageCode=12;
		$clGetTokenSession->UsernameToken = $UsernameToken;

		if ($UserExtranet == null)
			$clGetTokenSession->ExtranetUser = null;
		else
		{
			$clGetTokenSession->ExtranetUser = new ExtranetUserType();
			$clGetTokenSession->ExtranetUser->UsernameToken = $UserExtranet;
			$clGetTokenSession->ExtranetUser->Form = $FormExtranet;
		}

		return $clGetTokenSession;
	}


	/**
	 * Test l'identification avec des valeurs correctes
	 * @return mixed
	 */
	protected function _sGetTokenSession_TRUE()
	{
		$clReponseWS = $this->m_clNOUTOnline->GetTokenSession($this->_getGetTokenSession($this->_clGetUsernameToken()));
		$this->assertNotEquals(false, $clReponseWS->sGetTokenSession());
		return $clReponseWS->sGetTokenSession();
	}

	/**
	 * Teste l'identification avec des valeurs erronées
	 * @return false (toujours faux)
	 * @expectedException SOAPException
	 */
	protected function _sGetTokenSession_FALSE()
	{
		//identifiant faux
		$clReponseWS = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseure', ''));
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		//mot de passe faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseur', 'superviseur'));
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		return false;
	}

	/**
	 * Ferme une session
	 * @param $sTokenSession token de la session à fermer
	 * @param $bDoitReussir true si l'appel doit reussir
	 */
	protected function _Disconnect($sTokenSession, $bDoitReussir)
	{
		//Disconnect
		$clUsernameToken = $this->_clGetUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession);

		$ret = $this->m_clNOUTOnline->disconnect($TabHeader);
		if ($bDoitReussir)
			$this->assertEquals(true, $ret);
		else
			$this->assertEquals(false, $ret);
	}

	/**
	 * Valide la dernière action du contexte
	 * @param $nIDContexteAction
	 * @param $bDoitReussir
	 * @return boolean
	 */
	protected function _bValidate($nIDContexteAction, $bDoitReussir)
	{
		$ret = $this->m_clNOUTOnline->Validate($nIDContexteAction);
		if ($bDoitReussir)
			$this->assertNotEquals(false, $ret);
		else
			$this->assertEquals(false, $ret);

		return $ret;
	}

	/**
	 * Annule la dernière action ou le contexte d'action entier
	 * @param $nIDContexteAction
	 * @param $bTout
	 * @return boolean
	 */
	protected function _bCancel($nIDContexteAction, $bTout, $bDoitReussir)
	{
		$ret = $this->m_clNOUTOnline->Cancel($nIDContexteAction, $bTout);
		if ($bDoitReussir)
			$this->assertNotEquals(false, $ret);
		else
			$this->assertEquals(false, $ret);

		return $ret;
	}


	/**
	 * méthode pour tester l'identification (intranet uniquement)
	 */
	public function testGetTokenSession()
	{
		//on commence par tester le cas d'erreur
		$this->_sGetTokenSession_FALSE();
		$sTokenSession = $this->_sGetTokenSession_TRUE();

		//on déconnecte la session
		$this->_Disconnect($sTokenSession, true);

		//on teste avec une session qui n'existe pas
		$this->_Disconnect('aaaa-aaa-a--a', false);
	}

	/**
	 * Test la méthode de liste
	 */
	public function testList()
	{
		//ouverture de session
		$sTokenSession = $this->_sGetTokenSession_TRUE();
		if ($sTokenSession === false)
			return ; //pas la peine de continuer pas de session d'ouverte

		//--------------------------------------------------
		//on commence par tester la liste sans la pagination
		//pour cela on liste les utilisateurs, sur validator il y a plus de 20 utilisateurs

		$ret = $this->m_clNOUTOnline->GetList($sTokenSession, 'utilisateur');
		$this->assertGreaterThanOrEqual(20, $nNbUtilisateur);

		$this->_bValidate($nIDContexteAction, true);

		$this->_Disconnect($sTokenSession, true);
	}

	public function testDisplay()
	{


	}

} 