<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 12/12/2016
 * Time: 11:50
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;

/*
 * Parser pour le NOUVEAU planning (DHX)
 */

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage\LangageTableau;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserScheduler extends ParserList
{

    /** @var ParserXmlXsd */
    protected $m_clParserScheduler;

    /** @var RecordList|null */
    protected $m_clScheduler;


    public function __construct()
    {
        parent::__construct();
        $this->m_clParserScheduler = new ParserXmlXsd();
    }

    public function Parse(XMLResponseWS $clXMLReponseWS, $idForm)
    {
        parent::Parse($clXMLReponseWS, $idForm);

        // Parser les utilisateurs
        $this->_ParseScheduler($clXMLReponseWS);
    }


    /**
     * Parse le scheduler
     * Ne doit pas etre trop volumineuse
     * @param XMLResponseWS $clReponseXML
     * @throws \Exception
     */
    protected function _ParseScheduler(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeXSDRessource();
        $ndXML       = $clReponseXML->getNodeXMLRessource();
        $idForm      = LangageTableau::Ressource; //toutes les ressources sont fils du tableau ressource

        if (count($ndSchema)>0)
        {
            $this->m_clParserScheduler->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        if (count($ndXML)>0){
            $this->m_clParserScheduler->ParseXML($ndXML, $idForm, StructureElement::NV_XSD_List);
        }

        $clStructElem = $this->m_clParserList->getStructureElem($idForm, StructureElement::NV_XSD_List);

        //Instance d'une nouvelle clList avec toutes les donnees precedentes
        $this->m_clScheduler = new RecordList('', '', $idForm, $this->m_clParserScheduler->m_TabEnregTableau, $clStructElem, false,false, false, array(), array());

        // Param�tres pour la clList
        $this->m_clScheduler->setRecordCache($this->m_clParserScheduler->getFullCache());
    }

    /**
     * @return RecordList|null
     */
    public function getScheduler(): ?RecordList
    {
        return $this->m_clScheduler;
    }
}