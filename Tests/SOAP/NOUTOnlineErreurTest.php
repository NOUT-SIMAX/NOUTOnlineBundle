<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 28/07/14
 * Time: 16:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\OnlineError;
use NOUT\Bundle\NOUTOnlineBundle\OASIS\UsernameToken;

/**
 * classe qui teste les cas d'erreur
 * Class NOUTOnlineErreurTest
 * @package NOUT\Bundle\NOUTOnlineBundle\Tests\SOAP
 */
class NOUTOnlineErreurTest extends NOUTOnlineTest {


	/**
	 * Teste l'identification avec des valeurs erronées
	 */
	public function testGetTokenSession_ERREUR()
	{
		//identifiant faux
		$nErreur=0;
		$nCategorie=0;
		try{
			$clReponseWS = $this->m_clNOUTOnline->GetTokenSession($this->_getGetTokenSession(new UsernameToken('superviseureeeeeee', '')));
		}
		catch(\Exception $e)
		{
			$XMLResponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $XMLResponseWS->bIsFault());
			$nErreur = $XMLResponseWS->getNumError();
			$nCategorie = $XMLResponseWS->getCatError();
		}
		$this->assertEquals(OnlineError::CAT_SIMAXSERVICE, $nCategorie);
		$this->assertThat(
			$nErreur,
			$this->logicalOr(
				$this->equalTo(OnlineError::ERR_UTIL_NONRESOLU),
				$this->equalTo(OnlineError::ERR_UTIL_INCONNU)
			)
		);

		//mot de passe faux
		$nErreur=0;
		$nCategorie=0;
		try{
			$clReponseWS = $this->m_clNOUTOnline->GetTokenSession($this->_getGetTokenSession(new UserNameToken('superviseur', 'ttttt')));
		}
		catch(\Exception $e)
		{
			$XMLResponseWS = $this->m_clNOUTOnline->getXMLResponseWS();
			$this->assertEquals(true, $XMLResponseWS->bIsFault());
			$nErreur = $XMLResponseWS->getNumError();
			$nCategorie = $XMLResponseWS->getCatError();
		}
		$this->assertEquals(OnlineError::CAT_SIMAXSERVICE, $nCategorie);
		$this->assertEquals(OnlineError::ERR_UTIL_PASSERRINTRA, $nErreur);
	}

	/**
	 * Teste
	 */
	public function testDisconnect_ERREUR()
	{
		//Disconnect
		$clUsernameToken = $this->_clGetUsernameToken();
		$TabHeader=array('UsernameToken'=>$clUsernameToken, 'SessionToken'=>'aaaaaaaaaaaaaaaaaaaaaaaaaaaaa');

		//mot de passe faux
		$nErreur=0;
		$nCategorie=0;
		try{
			$this->m_clNOUTOnline->disconnect($TabHeader);
		}
		catch(\Exception $e)
		{
			$XMLResponseWS = $this->m_clNOUTOnline->getXMLResponseWS();

			$this->assertEquals(true, $XMLResponseWS->bIsFault());
			$nErreur = $XMLResponseWS->getNumError();
			$nCategorie = $XMLResponseWS->getCatError();

			//echo "\n$nCategorie $nErreur\n";
		}
		$this->assertEquals(OnlineError::CAT_SIMAXSERVICE, $nCategorie);
		$this->assertEquals(OnlineError::ERR_UTIL_DECONNECTE, $nErreur);
	}

} 