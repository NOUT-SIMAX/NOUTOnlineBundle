<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserRecord extends Parser
{
    /**@var ParserXmlXsd */
    protected $m_clParser;


    public function __construct()
    {
        $this->m_clParser = new ParserXmlXsd();
    }

    /**
     * @param XMLResponseWS $clXMLReponseWS
     * @throws \Exception
     */
    public function Parse(XMLResponseWS $clXMLReponseWS, $idForm)
    {
        $ndSchema    = $clXMLReponseWS->getNodeSchema();
        if (isset($ndSchema))
        {
            $this->m_clParser->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $idForm     = (!empty($idForm)) ? $idForm : $clXMLReponseWS->clGetForm()->getID();
        $ndXML = $clXMLReponseWS->getNodeXML();
        $this->m_clParser->ParseXML($ndXML, $idForm, StructureElement::NV_XSD_Enreg);
    }

    /**
     * @param XMLResponseWS $clResponseXML
     * @return null|Record
     */
    public function getRecord(XMLResponseWS $clResponseXML): ?Record
    {
        return $this->m_clParser->getRecord($clResponseXML);
    }

}