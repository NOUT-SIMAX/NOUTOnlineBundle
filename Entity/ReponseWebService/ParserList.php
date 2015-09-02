<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;

class ParserList extends Parser
{
    /**
     * @var ParserRecordList
     */
    protected $m_clParserList;

    /**
     * @var ParserRecordList
     */
    protected $m_clParserParam;

    public function __construct()
    {
        $this->m_clParserList = new ParserRecordList();
        $this->m_clParserParam = new ParserRecordList();
    }


    /**
     * Parse la liste
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseList(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeSchema();
        if (isset($ndSchema))
        {
            $this->m_clParserList->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        $ndXML = $clReponseXML->getNodeXML();
        $this->m_clParserList->ParseXML($ndXML, $clReponseXML->clGetForm()->getID(), StructureElement::NV_XSD_List);
    }

    /**
     * Parse la liste
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseParam(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeXSDParam();
        if (isset($ndSchema))
        {
            $this->m_clParserParam->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $ndXML = $clReponseXML->getNodeXMLParam();
        $this->m_clParserParam->ParseXML($ndXML, $clReponseXML->clGetAction()->getIDForm(), StructureElement::NV_XSD_Enreg);
    }

}