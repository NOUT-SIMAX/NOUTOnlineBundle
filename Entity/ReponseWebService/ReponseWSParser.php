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
     * @param XMLResponseWS $clXMLReponseWS
	 * @param $returnTypeForce
	 * @param $autreInfos
	 * @return Parser
	 */
	public function InitFromXmlXsd(XMLResponseWS $clXMLReponseWS, $returnTypeForce=null, $autreInfos=null)
	{
        // Tableau de pointeur de méthodes
		$aPtrFct = array(
			XMLResponseWS::RETURNTYPE_RECORD          	=> '_ParseRecord',
			XMLResponseWS::RETURNTYPE_VALIDATERECORD  	=> '_ParseRecord',
			XMLResponseWS::RETURNTYPE_VALIDATEACTION  	=> '_ParseRecord',

			XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION => '_ParseList',
			XMLResponseWS::RETURNTYPE_LIST            	=> '_ParseList',
			XMLResponseWS::RETURNTYPE_PRINTTEMPLATE   	=> '_ParseList',

			XMLResponseWS::RETURNTYPE_LISTCALCULATION 	=> '_ParseListCaculation',
			XMLResponseWS::RETURNTYPE_REPORT          	=> null,
			XMLResponseWS::RETURNTYPE_PLANNING        	=> '_ParsePlanning',
			XMLResponseWS::RETURNTYPE_CHART           	=> '_ParseChart',

            //cas particulier
            XMLResponseWS::RETURNTYPE_COLINRECORD 	    => '_ParseColInRecord',
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

		// Appel des fonctions à la volée grâce au tableau de méthodes
		return $this->$aPtrFct[$sReturnType]($clXMLReponseWS, $autreInfos);

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

    protected function _ParseColInRecord(XMLResponseWS $clReponseXML, $idColonne)
    {
        //on cherche le schema de la liste
        $ndSchema  = $clReponseXML->getNodeSchema();
        $xmlSchema = new \SimpleXMLElement($ndSchema->asXML());

        /*
<xs:element xs:name="id_52482111820129" simax:name="Liste Pays" simax:typeElement="simax-list" simax:printed="1" simax:withBtnOrder="1" simax:linkedTableXml="id_9495" simax:linkedTableID="9495" simax:withAddAndRemove="1">
<xs:complexType>
<xs:sequence>
<xs:element xs:name="id_9495" simax:name="Liste (Pays)" xs:minOccurs="0" xs:maxOccurs="unbounded">
         */
        $aData = $xmlSchema->xpath("//xs:element[@xs:name='id_$idColonne']");
        $clSchemaRoot = $aData[0]->children('xs', true)->complexType->children('xs', true)->sequence;
        $idForm = (string)$aData[0]->attributes('http://www.nout.fr/XMLSchema')['linkedTableID'];


        $ndXML  = $clReponseXML->getNodeXML();
        $xmlXML = new \SimpleXMLElement($ndXML->xml->asXML());

        $clParser = new ParserList();
        $clParser->ParseListFromSchemaAndXML($idForm, $xmlXML, $clSchemaRoot);

        return $clParser;
    }




	/**
	 * @param XMLResponseWS $clXMLReponseWS
     * @return ParserList
	 */
	protected function _ParseList(XMLResponseWS $clXMLReponseWS)
	{
		// Cette méthode est appelée par InitFromXmlXsd

		// Création d'un Parser de liste
        $clParser = new ParserList();

        // Parser les paramètres
        // Permet de savoir combien on a d'éléments avant de traiter les données ?
        $clParser->ParseParam($clXMLReponseWS);

		// Parser la liste
        $clParser->ParseList($clXMLReponseWS);


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
	 * @return ParserListCalculation
	 */
	protected function _ParseListCaculation(XMLResponseWS $clXMLReponseWS)
	{
		$clParser = new ParserListCalculation();

		$ndXML = $clXMLReponseWS->getNodeXML();
		$clParser->Parse($ndXML);

		return $clParser;
	}

}
