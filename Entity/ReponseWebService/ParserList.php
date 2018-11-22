<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
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
     * @return array|\NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray
     */
    public function GetTabEnregTableau()
    {
        return $this->m_clParserList->GetTabEnregTableau();
    }

    /**
     * @param $sIDForm
     * @param $sIDEnreg
     * @return \NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record|null
     */
    public function getRecordFromID($sIDForm, $sIDEnreg)
    {
        return $this->m_clParserList->getRecordFromID($sIDForm, $sIDEnreg);
    }

    /**
     * Parse la liste
     * Ne doit pas être trop volumineuse
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseList(XMLResponseWS $clReponseXML)
    {

        $ndSchema    = $clReponseXML->getNodeSchema();
        $ndXML = $clReponseXML->getNodeXML();
        $idForm = $clReponseXML->clGetForm()->getID();
        $exports = $clReponseXML->getExportsNode();
        $imports = $clReponseXML->getImportsNode();

        return $this->ParseListFromSchemaAndXML($idForm, $ndXML, $ndSchema, $imports, $exports);
    }

    /**
     * Parse la liste
     * Ne doit pas être trop volumineuse
     * @param                   $idForm
     * @param \SimpleXMLElement $ndXML
     * @param \SimpleXMLElement $ndSchema
     * @param \SimpleXMLElement $imports
     * @param \SimpleXMLElement $exports
     */
    public function ParseListFromSchemaAndXML($idForm, \SimpleXMLElement $ndXML, \SimpleXMLElement $ndSchema, $imports, $exports)
    {
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
     * Parse les paramètres
     * @param XMLResponseWS $clReponseXML
     */
    public function ParseParam(XMLResponseWS $clReponseXML)
    {
        $ndSchema    = $clReponseXML->getNodeXSDParam();

        // Ne pas utiliser isSet
        if (!is_null($ndSchema))
        {
            $this->m_clParserParam->ParseXSD($ndSchema, StructureElement::NV_XSD_Enreg);
        }

        $ndXML = $clReponseXML->getNodeXMLParam();

        // Ne pas utiliser isSet
        if (!is_null($ndXML))
        {
            $this->m_clParserParam->ParseXML($ndXML, $clReponseXML->clGetAction()->getIDForm(), StructureElement::NV_XSD_Enreg);
        }
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     */
    public function getList(XMLResponseWS $clReponseXML)
    {
        // Appel depuis le testController
        // GetRecord sur non-objet = ERREUR

        $sIDForm = $clReponseXML->clGetForm()->getID();
        $sIDFormAction = $clReponseXML->clGetAction()->getIDForm();
        $sIDAction = $clReponseXML->clGetAction()->getID();
        $sTitre = $clReponseXML->clGetAction()->getTitle();
        $withBtnOrder = $clReponseXML->clGetForm()->hasOrderBtn();
        $withOrderActive = $clReponseXML->clGetForm()->hasOrderActive();

        $exports = $this->_getExports();
        $imports = $this->_getImports();

        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);

        // Instance d'une nouvelle clList avec toutes les données précédentes
        $clList = new RecordList($sTitre, $sIDAction, $sIDForm, $this->m_clParserList->m_TabEnregTableau, $clStructElem, $withBtnOrder, $withOrderActive, $exports, $imports);
        $clList->setDefaultDisplayMode($clReponseXML->sGetDefaultDisplayMode());
        $clList->setTabPossibleDisplayMode($clReponseXML->GetTabPossibleDisplayMode());

        // Paramètres pour la clList
        $clList->setParam($this->m_clParserParam->getRecordFromID($sIDFormAction, $sIDAction));

        // TODO


        // Instance de ParserRecordList->getRecord()
        // Faire un getter du RecordCache du parserRecordList (get(get())
        // $this->m_clParserParam->

        // Deux méthodes dans ParserRecordList
        // GetFullCache et GetRecord
        // $this->m_clParserList est un ParserRecordList


        // Données pour la clList
        //// Il faut donner le cache en paramètre
        $clList->setRecordCache($this->m_clParserList->getFullCache());

        return $clList;
    }

    /**
     * @return array
     */
    protected function _getExports()
    {
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
        return $exports;
    }
    /**
     * @return array
     */
    protected function _getImports()
    {
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
        return $imports;
    }

    /**
     * @param XMLResponseWS $clReponseXML
     * @return RecordList
     * TODO: Refacto with above.
     */
    public function getSelectorList(XMLResponseWS $clReponseXML)
    {
        // Appel depuis le testController
        // GetRecord sur non-objet = ERREUR

        $sIDForm = $clReponseXML->clGetForm()->getID();
        $sIDFormAction = $clReponseXML->clGetAction()->getIDForm();
        $sIDAction = $clReponseXML->clGetAction()->getID();
        $sTitre = $clReponseXML->clGetTitle();

        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);


        $exports = $this->_getExports();
        $imports = $this->_getImports();

        // Instance d'une nouvelle clList avec toutes les données précédentes
        $clList = new RecordList($sTitre, $sIDAction, $sIDForm, $this->m_clParserList->m_TabEnregTableau, $clStructElem, false, false, $exports, $imports);
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
     * @return RecordCache
     */
    public function getListFullCache()
    {
        return $this->m_clParserList->getFullCache();
    }

}