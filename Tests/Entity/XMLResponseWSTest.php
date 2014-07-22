<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 22/07/14
 * Time: 14:51
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;


use NOUT\Bundle\NOUTOnlineBundle\Entity\CurrentAction;
use NOUT\Bundle\NOUTOnlineBundle\Entity\XMLResponseWS;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ConnectedUser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Element;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Form;

class XMLResponseWSTest extends \PHPUnit_Framework_TestCase
{
	/**
	 * @group entity
	 */
	public function testGetTokenSessionResponse()
	{
		$sXML = file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/xml/GetTokenSessionResponse.xml');
		$clXML = new XMLResponseWS($sXML);

		//return type
		$sReturnType = $clXML->sGetReturnType();
		$this->assertEquals(XMLResponseWS::RETURNTYPE_IDENTIFICATION, $sReturnType);

		//l'utilisateur connecté
		$clConnectedUser = $clXML->clGetConnectedUser();
		$sJSONTest = json_encode($clConnectedUser);

		$clConnectedUserAttendu = new ConnectedUser(2, 'superviseur', 1169, 'Utilisateur');
		$sJSONAttendu=json_encode($clConnectedUserAttendu);

		$this->assertEquals($sJSONAttendu, $sJSONTest);

		//token session
		$sToken = $clXML->sGetTokenSession();
		$this->assertEquals('6973456a-0452-4727-bf48-ee6cdd606390', $sToken);


	}

	/**
	 * @group entity
	 */
	public function testRecordResponse()
	{
		$sXML = file_get_contents('./src/NOUT/Bundle/NOUTOnlineBundle/Resources/public/test/xml/Record_Formulaire_Etat_Champ_ListeSync.xml');
		$clReponseXML = new XMLResponseWS($sXML);

		//return type
		$sReturnType = $clReponseXML->sGetReturnType();
		$this->assertEquals(XMLResponseWS::RETURNTYPE_RECORD, $sReturnType);

		//l'utilisateur connecté
		$clConnectedUserAttendu = new ConnectedUser(2, 'superviseur', 1169, 'Utilisateur');
		$sJSONAttendu=json_encode($clConnectedUserAttendu);

		$clConnectedUser = $clReponseXML->clGetConnectedUser();
		$sJSONTest = json_encode($clConnectedUser);

		$this->assertEquals($sJSONAttendu, $sJSONTest);

		//le contexte d'action
		$sContexteAction = $clReponseXML->sGetActionContext();
		$this->assertEquals('21797975181314', $sContexteAction);

		//l'action courante
		$clActionCouranteAttendu = new CurrentAction('17128382653213070409', 'Modifier Formulaire Etat de champ', 2387);
		$sJSONAttendu = json_encode($clActionCouranteAttendu);

		$clActionCourante = $clReponseXML->clGetAction();
		$sJSONTest = json_encode($clActionCourante);

		$this->assertEquals($sJSONAttendu, $sJSONTest);

		//formulaire
		$clFormAttendu = new Form('47909919412330', 'Formulaire Etat de champ');
		$sJSONAttendu = json_encode($clFormAttendu);

		$clForm = $clReponseXML->clGetForm();
		$sJSONTest = json_encode($clForm);

		$this->assertEquals($sJSONAttendu, $sJSONTest);

		//l'enregistrement
		$clElementAttendu = new Element('33475861129246', 'Janvier');
		$sJSONAttendu = json_encode($clElementAttendu);

		$clElement = $clReponseXML->clGetElement();
		$sJSONTest = json_encode($clElement);

		$this->assertEquals($sJSONAttendu, $sJSONTest);

		//quelque vérification sur le schema
		$clSchema=$clReponseXML->getNodeSchema();
		$this->assertNotNull($clSchema);

		//verif des versions
		$this->assertEquals('1', $clSchema->attributes()->VERSION);
		$this->assertEquals('1', $clSchema->attributes()->VERSION_LECTECRIT);
		$this->assertEquals('1', $clSchema->attributes()->VERSION_LECTURE);

		//quelque vérification sur le xml
		$clXML = $clReponseXML->getNodeXML('Modify');
		$this->assertNotNull($clXML);

		//verif des versions
		$this->assertEquals('1', $clXML->attributes()->VERSION);
		$this->assertEquals('1', $clXML->attributes()->VERSION_LECTECRIT);
		$this->assertEquals('1', $clXML->attributes()->VERSION_LECTURE);

		//de la date heure
		$this->assertEquals('2014071812351242', $clXML->attributes()->DATEHEURE);


	}


} 