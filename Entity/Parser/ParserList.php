<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordCache;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserList extends ParserWithParam
{
    /**
     * @var ParserXmlXsd
     */
    protected $m_clParserList;


    public function __construct()
    {
        parent::__construct();
        $this->m_clParserList = new ParserXmlXsd();
    }

    /**
     * @return EnregTableauArray
     */
    public function GetTabEnregTableau() : EnregTableauArray
    {
        return $this->m_clParserList->GetTabEnregTableau();
    }

    /**
     * @param $sIDForm
     * @param $sIDEnreg
     * @return Record|null
     */
    public function getRecordFromID($sIDForm, $sIDEnreg): ?Record
    {
        return $this->m_clParserList->getRecordFromID($sIDForm, $sIDEnreg);
    }

    /**
     * Parse la liste
     * Ne doit pas être trop volumineuse
     * @param XMLResponseWS $clXMLReponseWS
     * @throws \Exception
     */
    public function Parse(XMLResponseWS $clXMLReponseWS)
    {
        // Parser les paramètres
        // Permet de savoir combien on a d'éléments avant de traiter les données ?
        parent::Parse($clXMLReponseWS);

        $ndSchema   = $clXMLReponseWS->getNodeSchema();
        $ndXML      = $clXMLReponseWS->getNodeXML();
        $idForm     = $clXMLReponseWS->clGetForm()->getID();
        $exports    = $clXMLReponseWS->getExportsNode();
        $imports    = $clXMLReponseWS->getImportsNode();

        if (isset($ndSchema))
        {
            $this->m_clParserList->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        if(!is_null($imports)) {
            $this->m_clParserList->setImports($imports);
        }

        if(!is_null($exports)) {
            $this->m_clParserList->setExports($exports);
        }

        $this->m_clParserList->ParseXML($ndXML, $idForm, StructureElement::NV_XSD_List);
    }


    /**
     * @param XMLResponseWS $clReponseXML
     * @param $aTabSort
     * @param $bActiveReorder
     * @param $bPossibleReorder
     * @return RecordList
     */
    protected function _getRecordList(XMLResponseWS $clReponseXML, $bPossibleReorder, $bActiveReorder, $aTabSort): RecordList
    {
        $clAction = $clReponseXML->clGetAction();
        $sIDFormAction = $clAction->getIDForm();
        $sIDAction = $clAction->getID();
        $sTitre =$clAction->getTitle();

        $clForm = $clReponseXML->clGetForm();
        $sIDForm = $clForm->getID();

        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);


        $exports = $this->_getExports();
        $imports = $this->_getImports();

        // Instance d'une nouvelle clList avec toutes les données précédentes
        $clList = new RecordList($sTitre, $sIDAction, $sIDForm, $this->m_clParserList->m_TabEnregTableau, $clStructElem, $bPossibleReorder, $bActiveReorder, $exports, $imports, $aTabSort);
        $clList->setDefaultDisplayMode($clReponseXML->sGetDefaultDisplayMode());
        $clList->setTabPossibleDisplayMode($clReponseXML->GetTabPossibleDisplayMode());

        // Paramètres pour la clList
        $clList->setParam($this->m_clParserParam->getRecordFromID($sIDFormAction, $sIDAction));

        // Données pour la clList
        //// Il faut donner le cache en paramètre
        $clList->setRecordCache($this->m_clParserList->getFullCache());

        return $clList;
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     */
    public function getList(XMLResponseWS $clReponseXML): RecordList
    {
        $clForm = $clReponseXML->clGetForm();
        $withBtnOrder = $clForm->hasOrderBtn();
        $withOrderActive = $clForm->hasOrderActive();
        $tabSort = $clForm->getTabSort();

        return $this->_getRecordList($clReponseXML, $withBtnOrder, $withOrderActive, $tabSort);
    }


    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     */
    public function getSelectorList(XMLResponseWS $clReponseXML) : RecordList
    {
        return $this->_getRecordList($clReponseXML, false, false, array());
    }

    /**
     * @return array
     */
    protected function _getExports(): array
    {
        $exports = array();
        if(!is_null($this->m_clParserList->getExports())) {
            foreach($this->m_clParserList->getExports() as $xmlExport) {
                /** @var \SimpleXMLElement $xmlExport */
                $export = new \stdClass();
                foreach($xmlExport->attributes() as $name => $value) {
                    $export->$name = (string) $value;
                }
                $export->value = (string) $xmlExport;
                array_push($exports, $export);
            }
        }
        return $exports;
    }
    /**
     * @return array
     */
    protected function _getImports(): array
    {
        $imports = array();
        if(!is_null($this->m_clParserList->getImports())) {
            foreach($this->m_clParserList->getImports() as $xmlImport) {
                /** @var \SimpleXMLElement $xmlImport */

                $import = new \stdClass();
                foreach($xmlImport->attributes() as $name => $value) {
                    $import->$name = (string) $value;
                }
                $import->value = (string) $xmlImport;
                array_push($imports, $import);
            }
        }
        return $imports;
    }
}