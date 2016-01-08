<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 10:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\ColonneRestriction;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\InfoColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureBouton;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureDonnee;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureSection;

/**
 * Class RecordManager
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity\Record
 */
class ReponseWSParser
{

	/**
	 * @param $sReturnType
	 * @param \SimpleXMLElement $clXML
	 * @param \SimpleXMLElement $clSchema
	 * @return Parser
	 */
	public function InitFromXmlXsd(XMLResponseWS $clXMLReponseWS)
	{
		$aPtrFct = array(
			XMLResponseWS::RETURNTYPE_RECORD          => '_ParseRecord',
			XMLResponseWS::RETURNTYPE_VALIDATERECORD  => '_ParseRecord',
			// XMLResponseWS::RETURNTYPE_VALIDATEACTION  => plus tard

			XMLResponseWS::RETURNTYPE_AMBIGUOUSACTION => '_ParseList',
			XMLResponseWS::RETURNTYPE_LIST            => '_ParseList',
			XMLResponseWS::RETURNTYPE_PRINTTEMPLATE   => '_ParseList',

			XMLResponseWS::RETURNTYPE_LISTCALCULATION => '_ParseListCaculation',
			XMLResponseWS::RETURNTYPE_REPORT          => null,
			XMLResponseWS::RETURNTYPE_PLANNING        => '_ParsePlanning',
			XMLResponseWS::RETURNTYPE_CHART           => '_ParseChart',
		);
		// Tableau de pointeur de méthodes

		$sReturnType = $clXMLReponseWS->sGetReturnType();
		if (!array_key_exists($sReturnType, $aPtrFct))
		{
			throw new \Exception('type de retour "'.$sReturnType.'" non gérée au niveau du parseur');
		}

		if (is_null($aPtrFct[$sReturnType]))
		{
			return null;
		}

		// Appel des fonctions à la volée grâce au tableau de méthodes
		return $this->$aPtrFct[$sReturnType]($clXMLReponseWS);

		// Dans le bug c'est _ParseList qui est renvoyé
		// Le type de paramètre était donc RETURNTYPE_LIST (liste de villes)
	}

	/**
	 * @param XMLResponseWS $clXMLReponseWS
     * @return ParserRecordList
	 */
	protected function _ParseRecord(XMLResponseWS $clXMLReponseWS)
	{
        $clParser = new ParserRecordList();

        $ndSchema    = $clXMLReponseWS->getNodeSchema();
        if (isset($ndSchema))
        {
            $clParser->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $ndXML = $clXMLReponseWS->getNodeXML();
        $clParser->ParseXML($ndXML, $clXMLReponseWS->clGetForm()->getID(), StructureElement::NV_XSD_Enreg);

        return $clParser;
	}

	/**
	 * @param XMLResponseWS $clXMLReponseWS
     * @return ParserRecordList
	 */
	protected function _ParseList(XMLResponseWS $clXMLReponseWS)
	{
		// Cette méthode est appelée par InitFromXmlXsd

		// Création d'un Parser de liste
        $clParser = new ParserList();

		// Parser la liste
        $clParser->ParseList($clXMLReponseWS);

		// Parser les paramètres
        $clParser->ParseParam($clXMLReponseWS);

        return $clParser;
	}

	/**
	 * @param XMLResponseWS $clXMLReponseWS
	 * @return ParserPlanning
	 */
	protected function _ParsePlanning(XMLResponseWS $clXMLReponseWS)
	{
		$clParser = new ParserPlanning();

		$ndSchema    = $clXMLReponseWS->getNodeSchema();
		$clParser->TypeEvent2Color($ndSchema);

		$ndXML       = $clXMLReponseWS->getNodeXML();
		$clParser->Parse($ndXML);

		return $clParser;
	}

	/**
	 * @param XMLResponseWS $clXMLReponseWS
	 * @return ParserChart
	 */
	protected function _ParseChart(XMLResponseWS $clXMLReponseWS)
	{
		$clParser = new ParserChart();

		$ndXML = $clXMLReponseWS->getNodeXML();
		$clParser->Parse($ndXML);

		return $clParser;
	}


	/**
	 * Parse les calculs de fin de liste
	 * @param \XMLResponseWS $clXMLReponseWS
	 */
	protected function _ParseListCaculation(XMLResponseWS $clXMLReponseWS)
	{
		$clParser = new ParserListCalculation();

		$ndXML = $clXMLReponseWS->getNodeXML();
		$clParser->Parse($ndXML);

		return $clParser;
	}

}
