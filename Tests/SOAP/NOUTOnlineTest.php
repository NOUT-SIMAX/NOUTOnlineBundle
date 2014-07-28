<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\DataCollector\NOUTOnlineLogger;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConfigurationDialogue;
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
		$sService = 'http://127.0.0.1:8062';
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

		//le logger
		$clLogger = new NOUTOnlineLogger(null, false);

		//ici on instancie NOUTOnline
		$this->m_clNOUTOnline = new OnlineServiceProxy($this->m_clConfig, $clLogger);
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
	 * Teste l'identification avec des valeurs erronées
	 */
	public function testGetTokenSession_FALSE()
	{
		//identifiant faux
		$nExceptionCode=0;
		try{
			$clReponseWS = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseureeeeeee', ''));
		}
		catch(\Exception $e)
		{
			$nExceptionCode=$e->getCode();
		}
		$this->assertEquals(1404, $nExceptionCode);

		//mot de passe faux
		$nExceptionCode=0;
		try{
			$clReponseWS = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseur', 'ttttt'));
		}
		catch(\Exception $e)
		{
			$nExceptionCode=$e->getCode();
		}
		$this->assertEquals(1403, $nExceptionCode);
	}

	/**
	 * Teste
	 */
	public function testDisconnect_FALSE()
	{
		//Disconnect
		$clUsernameToken = $this->_clGetUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

		//mot de passe faux
		$nExceptionCode=0;
		try{
			$this->m_clNOUTOnline->disconnect($TabHeader);
		}
		catch(\Exception $e)
		{
			$nExceptionCode=$e->getCode();
		}
		$this->assertNotEquals(0, $nExceptionCode);
	}

	/**
	 * Test l'identification avec des valeurs correctes
	 * @return string
	 */
	protected function testGetTokenSession()
	{
		$clReponseWS = $this->m_clNOUTOnline->GetTokenSession($this->_getGetTokenSession($this->_clGetUsernameToken()));
		$sTokenSession = $clReponseWS->sGetTokenSession();
		$this->assertNotEquals('', $sTokenSession);
		return $sTokenSession;
	}


	/**
	 * Ferme une session
	 * @param $sTokenSession token de la session à fermer
	 * @depends testGetTokenSession
	 */
	protected function testDisconnect($sTokenSession)
	{
		//Disconnect
		$clUsernameToken = $this->_clGetUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>$sTokenSession);

		//mot de passe faux
		$nExceptionCode=0;
		try{
			$this->m_clNOUTOnline->disconnect($TabHeader);
		}
		catch(\Exception $e)
		{
			$nExceptionCode=$e->getCode();
		}
		$this->assertEquals(0, $nExceptionCode);
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
	 * @param $sTokenSession
	 * @depends testGetTokenSession
	 */
	public function testDisplay($sTokenSession)
	{


	}

} 