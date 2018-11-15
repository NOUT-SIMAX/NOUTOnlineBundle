<?php
/**
 * Created by PhpStorm.
 * User: Chris
 * Date: 12/12/2016
 * Time: 11:50
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/*
 * Parser pour le NOUVEAU planning (DHX)
 */

use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;

class ParserScheduler extends ParserList
{

    /**
     * @var ParserRecordList
     */
    protected $m_clParserScheduler;


    public function __construct()
    {
        parent::__construct();
        $this->m_clParserScheduler = new ParserRecordList();
    }


    /**
     * Parse le scheduler
     * Ne doit pas etre trop volumineuse
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseScheduler(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeXSDRessource();
        $ndXML       = $clReponseXML->getNodeXMLRessource();
        $idForm      = Langage::TABL_Ressource; //toutes les ressources sont fils du tableau ressource

        if (count($ndSchema)>0)
        {
            $this->m_clParserScheduler->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        if (count($ndXML)>0){
            $this->m_clParserScheduler->ParseXML($ndXML, $idForm, StructureElement::NV_XSD_List);
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     */
    public function getScheduler(XMLResponseWS $clReponseXML)
    {
        $sIDForm        = Langage::TABL_Ressource;

        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);

        $exports = array();
        if(!is_null($this->m_clParserList->getExports())) {
            foreach($this->m_clParserList->getExports() as $xmlExport) {
                $export = new \stdClass();
                foreach($xmlExport->attributes() as $name => $value) {
                    $export->$name = (string) $value;
                }
                $export->value = (string) $xmlExport;
                array_push($exports, $export);
            }
        }

        $imports = array();
        if(!is_null($this->m_clParserList->getImports())) {
            foreach($this->m_clParserList->getImports() as $xmlImport) {
                $import = new \stdClass();
                foreach($xmlImport->attributes() as $name => $value) {
                    $import->$name = (string) $value;
                }
                $import->value = (string) $xmlImport;
                array_push($imports, $import);
            }
        }

        // Instance d'une nouvelle clList avec toutes les donn�es pr�c�dentes
        $clList = new RecordList('', '', $sIDForm, $this->m_clParserScheduler->m_TabEnregTableau, $clStructElem, false, false, $exports, $imports);

        // Param�tres pour la clList
        $clList->setRecordCache($this->m_clParserScheduler->getFullCache());
        return $clList;
    }
}