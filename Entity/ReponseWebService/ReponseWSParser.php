<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 10:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\Parser;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserListCalculation;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserNumberOfChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserPlanning;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserRecord;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parser\ParserScheduler;

/**
 * Class RecordManager
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class ReponseWSParser
{

    /**
     * @param XMLResponseWS $clXMLReponseWS
     * @param null          $idForm
     * @return Parser
     * @throws \Exception
     */
	public function InitFromXmlXsd(XMLResponseWS $clXMLReponseWS, $idForm=null) : ?Parser
	{
        // Tableau de pointeur de méthodes
		$aPtrFct = array(
            XMLResponseWS::RETURNTYPE_RECORD          	=> ParserRecord::class,
            XMLResponseWS::RETURNTYPE_VALIDATERECORD  	=> ParserRecord::class,
            XMLResponseWS::RETURNTYPE_VALIDATEACTION  	=> ParserRecord::class,

            XMLResponseWS::RETURNTYPE_SCHEDULER 		=> ParserScheduler::class,
            XMLResponseWS::RETURNTYPE_THUMBNAIL         => ParserList::class,
            XMLResponseWS::RETURNTYPE_DATATREE          => ParserList::class,
            XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION => ParserList::class,
            XMLResponseWS::RETURNTYPE_LIST            	=> ParserList::class,
            XMLResponseWS::RETURNTYPE_GLOBALSEARCH      => ParserList::class,
            XMLResponseWS::RETURNTYPE_PRINTTEMPLATE   	=> ParserList::class,
            XMLResponseWS::RETURNTYPE_CHOICE           	=> ParserList::class,
            XMLResponseWS::RETURNTYPE_REQUESTFILTER     => ParserList::class,
            XMLResponseWS::RETURNTYPE_MAILSERVICELIST   => ParserList::class,
            XMLResponseWS::RETURNTYPE_NUMBEROFCHART     => ParserNumberOfChart::class,

            XMLResponseWS::RETURNTYPE_LISTCALCULATION 	=> ParserListCalculation::class,
            XMLResponseWS::RETURNTYPE_REPORT          	=> null,
            XMLResponseWS::RETURNTYPE_PLANNING        	=> ParserPlanning::class,
            XMLResponseWS::RETURNTYPE_CHART           	=> ParserChart::class,
		);


		$sReturnType = empty($returnTypeForce) ? $clXMLReponseWS->sGetReturnType() : $returnTypeForce;
		if (!array_key_exists($sReturnType, $aPtrFct))
		{
			throw new \Exception('type de retour "'.$sReturnType.'" non gérée au niveau du parseur');
		}

		if (is_null($aPtrFct[$sReturnType]))
		{
			return null;
		}

        $parserClass = $aPtrFct[$sReturnType];

		/** @var Parser $clParser */
		$clParser = new $parserClass();
		$clParser->Parse($clXMLReponseWS, $idForm);

		// Appel des fonctions à la volée grâce au tableau de méthodes
		return $clParser;

	}
}
