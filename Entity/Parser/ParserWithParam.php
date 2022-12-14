<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserWithParam extends Parser
{
    /** @var ParserXmlXsd */
    protected $m_clParserParam;

    public function __construct()
    {
        $this->m_clParserParam = new ParserXmlXsd();
    }

    /**
     * Parse les paramètres
     * @param XMLResponseWS $clXMLReponseWS
     * @throws \Exception
     */
    public function Parse(XMLResponseWS $clXMLReponseWS, $idForm)
    {
        $ndSchema    = $clXMLReponseWS->getNodeXSDParam();

        // Ne pas utiliser isSet
        if (!is_null($ndSchema))
        {
            $this->m_clParserParam->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $ndXML = $clXMLReponseWS->getNodeXMLParam();

        // Ne pas utiliser isSet
        if (!is_null($ndXML))
        {
            $this->m_clParserParam->ParseXML($ndXML, $clXMLReponseWS->clGetAction()->getIDForm(), StructureElement::NV_XSD_Enreg);
        }
    }


    /**
     * @param \SimpleXMLElement|null $ndXml
     * @return array
     */
    protected function _getImportOrExportArray(\SimpleXMLElement $ndXml=null) : array
    {
        $ret = array();
        if(!is_null($ndXml)) {
            foreach($ndXml as $ndChild) {
                /** @var \SimpleXMLElement $ndChild */
                $obj = new \stdClass();
                foreach($ndChild->attributes() as $name => $value) {
                    $obj->$name = (string) $value;
                }
                $obj->value = (string) $ndChild;
                array_push($ret, $obj);
            }
        }
        return $ret;
    }
}