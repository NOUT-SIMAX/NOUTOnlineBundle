<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/09/2015
 * Time: 16:34
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\RecordList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableauArray;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserList extends ParserWithParam
{
    /** @var ParserXmlXsd */
    protected $m_clParserList;

    /** @var RecordList|null */
    protected $m_clRecordList;


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
    public function Parse(XMLResponseWS $clXMLReponseWS, $idForm)
    {
        // Parser les paramètres
        // Permet de savoir combien on a d'éléments avant de traiter les données ?
        parent::Parse($clXMLReponseWS, $idForm);

        $ndSchema   = $clXMLReponseWS->getNodeSchema();
        $ndXML      = $clXMLReponseWS->getNodeXML();
        $clForm     = $clXMLReponseWS->clGetForm();
        $idForm     = (!empty($idForm)) ? $idForm : $clForm->getID();

        if (isset($ndSchema))
        {
            $this->m_clParserList->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
        }

        $this->m_clParserList->ParseXML($ndXML, $idForm, StructureElement::NV_XSD_List);



        $returnType = $clXMLReponseWS->sGetReturnType();
        if (    ($returnType == XMLResponseWS::RETURNTYPE_PRINTTEMPLATE)
            ||  ($returnType == XMLResponseWS::RETURNTYPE_AMBIGUOUSCREATION)
            ||  ($returnType == XMLResponseWS::RETURNTYPE_CHOICE)
            ||  ($returnType == XMLResponseWS::RETURNTYPE_MAILSERVICELIST))
        {
            //on pas besoin des exports, tri et autre
            $this->m_clRecordList = $this->_getRecordList($clXMLReponseWS, false,false, false, array(), null, null);
            return ;
        }

        $withBtnOrder = $clForm->hasOrderBtn();
        $withOrderActive = $clForm->hasOrderActive();
        $withGhost = $clForm->getWithGhost();
        $tabSort = $clForm->getTabSort();
        $exports    = $clXMLReponseWS->getExportsNode();
        $imports    = $clXMLReponseWS->getImportsNode();

        $this->m_clRecordList = $this->_getRecordList($clXMLReponseWS, $withGhost, $withBtnOrder, $withOrderActive, $tabSort, $exports, $imports);
    }


    /**
     * @param XMLResponseWS $clReponseXML
     * @param $aTabSort
     * @param $bActiveReorder
     * @param $bPossibleReorder
     * @return RecordList
     */
    protected function _getRecordList(XMLResponseWS $clReponseXML, $bWithGhost, $bPossibleReorder, $bActiveReorder, $aTabSort, $exportsNode, $importsNode): RecordList
    {
        $clAction = $clReponseXML->clGetAction();
        $sIDFormAction = $clAction->getIDForm();
        $sIDAction = $clAction->getID();
        $sTitre =$clAction->getTitle();

        $clForm = $clReponseXML->clGetForm();
        $sIDForm = $clForm->getID();

        $clStructElem = $this->m_clParserList->getStructureElem($sIDForm, StructureElement::NV_XSD_List);

        $exports = $this->_getImportOrExportArray($exportsNode);
        $imports = $this->_getImportOrExportArray($importsNode);

        // Instance d'une nouvelle clList avec toutes les données précédentes
        $clList = new RecordList($sTitre, $sIDAction, $sIDForm, $this->m_clParserList->m_TabEnregTableau, $clStructElem, $bWithGhost, $bPossibleReorder, $bActiveReorder, $exports, $imports, $aTabSort);
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
     * @return RecordList
     */
    public function getList(): ?RecordList
    {
        return $this->m_clRecordList;
    }

}