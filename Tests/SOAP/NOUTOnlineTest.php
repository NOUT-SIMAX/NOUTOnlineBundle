<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 09:59
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


class NOUTOnlineTest extends \PHPUnit_Framework_TestCase {

	protected $m_sAdresseServeur = '127.0.0.1:8052';
	protected $m_sUtilisateur = 'superviseur';
	protected $m_sMotDePasse = '';

	protected function _aGetUsernameToken()
	{
		return array('user'=>$this->m_sUtilisateur, '');

	}



	public function testGetTokenSession()
	{
		$sAdresseServeur="127.0.0.1:8052";
		$sUtilisateur="superviseur";
		$sMotDePasse="";
		$sCreated=date(DATE_RFC2822);
		$sNonce=base64_encode($sCreated);

		//on instancie NOUTOnlineSOAP
		$clNOUTOnline = ""; //ici

		//on appelle la méthode GetTokeSession()

		//on appelle Disconnect pour déconnecter la session
	}

	public function testList()
	{



	}

} 