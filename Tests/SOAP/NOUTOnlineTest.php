<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\OASIS\UserNameToken;

/**
 * Class NOUTOnlineTest
 * classe pour tester NOUTOnline en mode Intranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 */
class NOUTOnlineTest extends \PHPUnit_Framework_TestCase
{
	protected $m_sAdresseServeur = '127.0.0.1:8052';
	protected $m_clNOUTOnline;

	public function __construct()
	{
		//ici on instancie NOUTOnline
		//$this->m_clNOUTOnline = new NOUTOnline($this->m_sAdresseServeur);
	}

	protected function _clGetUsernameToken()
	{
		return new UserNameToken('superviseur', '');
	}

	/**
	 * Test l'identification avec des valeurs correctes
	 * @return mixed
	 */
	protected function _sGetTokenSession_TRUE()
	{
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession($this->_clGetUsernameToken());
		$this->assertNotEquals($sTokenSession, false);
		return $sTokenSession;
	}

	/**
	 * Teste l'identification avec des valeurs erronées
	 * @return false (toujours faux)
	 */
	protected function _sGetTokenSession_FALSE()
	{
		//identifiant faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseure', ''));
		$this->assertEquals($sTokenSession, false);
		//TODO pouvoir tester le code d'erreur de retour

		//mot de passe faux
		$sTokenSession = $this->m_clNOUTOnline->GetTokenSession(UserNameToken('superviseur', 'superviseur'));
		$this->assertEquals($sTokenSession, false);
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