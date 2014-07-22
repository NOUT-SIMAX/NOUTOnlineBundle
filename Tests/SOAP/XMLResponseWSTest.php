<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 22/07/14
 * Time: 14:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Entity\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConnectedUser;

class XMLResponseWSTest extends \PHPUnit_Framework_TestCase
{
	public function testGetTokenSessionResponse()
	{
		$sXML = file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/xml/GetTokenSessionResponse.xml');
		$clXML = new XMLResponseWS($sXML);

		//token session
		$sToken = $clXML->sGetTokenSession();
		$this->assertEquals('6973456a-0452-4727-bf48-ee6cdd606390', $sToken);

		//return type
		$sReturnType = $clXML->sGetReturnType();
		$this->assertEquals(XMLResponseWS::RETURNTYPE_IDENTIFICATION, $sReturnType);

		//l'utilisateur connecté
		$clConnectedUser = $clXML->clGetConnectedUser();
		$sJSONTest = json_encode($clConnectedUser);

		$clConnectedUserAttendu = new ConnectedUser(2, 'superviseur', 1169, 'Utilisateur');
		$sJSONAttendu=json_encode($clConnectedUserAttendu);

		$this->assertEquals($sJSONAttendu, $sJSONTest);
	}
} 