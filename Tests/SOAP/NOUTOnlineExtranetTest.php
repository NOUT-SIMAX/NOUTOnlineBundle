<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 11:12
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;

/**
 * Class NOUTOnlineExtranetTest
 * classe pour tester NOUTOnline en mode extranet
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 */
class NOUTOnlineExtranetTest extends \PHPUnit_Framework_TestCase
{
	protected $m_sAdresseServeur = '127.0.0.1:8052';
	protected $m_clUsernameToken;
	protected $m_clUsernameTokenExtranet;
	protected $m_sFormExtranet ='Util Extranet';

	public function __construct()
	{
		$this->m_clUsernameToken = new UserNameToken('extranet authentifié', '');
		$this->m_clUsernameTokenExtranet = new UserNameToken('conan', 'conan');
	}

	/**
	 * méthode pour tester l'identification
	 */
	public function testGetTokenSession()
	{
		//on instancie NOUTOnlineSOAP
		$clNOUTOnline = ""; //ici

		//on appelle la méthode GetTokeSession()

		//on appelle Disconnect pour déconnecter la session
	}


	public function testList()
	{



	}

	public function testDisplay()
	{


	}
} 