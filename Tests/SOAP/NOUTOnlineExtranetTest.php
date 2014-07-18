<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 11:12
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;
use NOUT\Bundle\NOUTOnlineBundle\OASIS\UserNameToken;

/**
 * Class NOUTOnlineExtranetTest
 * classe pour tester NOUTOnline en mode extranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 */
class NOUTOnlineExtranetTest extends \PHPUnit_Framework_TestCase
{
	protected $m_sAdresseServeur = '127.0.0.1:8052';
	protected $m_sFormExtranet ='Util Extranet';
	protected $m_clNOUTOnline;

	public function __construct()
	{
		//ici on instancie NOUTOnline
		//$this->m_clNOUTOnline = new NOUTOnline($this->m_sAdresseServeur);
	}

	/**
	 * retourne le username token de l'utilisateur simax avec les bonnes informations
	 * @return UserNameToken
	 */
	protected function _clGetUsernameTokenSIMAX()
	{
		return new UserNameToken('extranet authentifié', '');
	}

	/**
	 * retourne le username token de l'utilisateur extranet avec les bonnes informations
	 * @return UserNameToken
	 */
	protected function _clGetUsernameTokenExtranet()
	{
		return new UserNameToken('conan', 'conan');
	}

	/**
	 * Test l'identification avec des valeurs correctes
	 * @return mixed
	 */
	protected function _sGetTokenSession_TRUE()
	{
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession($this->_clGetUsernameTokenSIMAX(), $this->_clGetUsernameTokenExtranet(), $this->m_sFormExtranet);
		$this->assertNotEquals(false, $sTokenSession);
		return $sTokenSession;
	}

	/**
	 * Teste l'identification avec des valeurs erronées
	 * @return false (toujours faux)
	 */
	protected function _sGetTokenSession_FALSE()
	{
		//-------------------------------------
		//Test d'erreur sur l'utilisateur SIMAX
		//identifiant faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('extranet faux', ''), $this->_clGetUsernameTokenExtranet(), $this->m_sFormExtranet);
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		//mot de passe faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('extranet authentifié', 'erreur de mot de passe'), $this->_clGetUsernameTokenExtranet(), $this->m_sFormExtranet);
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		//---------------------------------------
		//Test d'erreur sur le formulaire extranet
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession($this->_clGetUsernameTokenSIMAX(), $this->_clGetUsernameTokenExtranet(), 'utilisateur extranet');
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		//---------------------------------------
		//test d'erreur sur l'utilisateur Extranet
		//identifiant faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession($this->_clGetUsernameTokenSIMAX(), new UserNameToken('toto', 'toto'), $this->m_sFormExtranet);
		$this->assertEquals(false, $sTokenSession);
		//TODO pouvoir tester le code d'erreur de retour

		//mot de passe faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession($this->_clGetUsernameTokenSIMAX(), new UserNameToken('conan', ''), $this->m_sFormExtranet);
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
		$ret = $this->m_clNOUTOnline->Diconnect($this->_clGetUsernameToken(), $sTokenSession);
		if ($bDoitReussir)
			$this->assertNotEquals($ret, false);
		else
			$this->assertEquals($ret, false);
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


	public function testList()
	{



	}

	public function testDisplay()
	{


	}
} 