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

	/**
	 * renvoi le username token avec les bonnes infomations pour la connexion
	 * @return UserNameToken
	 */
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
		$this->assertNotEquals(false, $sTokenSession);
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
		$ret = $this->m_clNOUTOnline->Diconnect($this->_clGetUsernameToken(), $sTokenSession);
		if ($bDoitReussir)
			$this->assertNotEquals(false, $ret);
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