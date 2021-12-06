<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\NumberOfChart;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\StructureElement;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserNumberOfChart extends ParserWithParam
{
    /** @var NumberOfChart|null  */
    protected $m_clNbOfChart;


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

        $clAction = $clXMLReponseWS->clGetAction();
        $sIDFormAction = $clAction->getIDForm();
        $sIDAction = $clAction->getID();
        $sTitre =$clAction->getTitle();

        $clForm = $clXMLReponseWS->clGetForm();
        $sIDForm = $clForm->getID();

        $withBtnOrder = $clForm->hasOrderBtn();
        $withOrderActive = $clForm->hasOrderActive();
        $tabSort = $clForm->getTabSort();
        $withGhost = $clForm->getWithGhost();

        $exports = $this->_getImportOrExportArray($clXMLReponseWS->getExportsNode());
        $imports = $this->_getImportOrExportArray($clXMLReponseWS->getImportsNode());

        $ndSchema   = $clXMLReponseWS->getNodeSchema();
        $clStructElem = null;
        if (isset($ndSchema))
        {
            $clParserStruct = new ParserXmlXsd();

            $clParserStruct->ParseXSD($ndSchema, StructureElement::NV_XSD_List);
            $clStructElem = $clParserStruct->getStructureElem($sIDForm, StructureElement::NV_XSD_List);
        }

        // Instance d'une nouvelle clList avec toutes les données précédentes
        $this->m_clNbOfChart = new NumberOfChart($sTitre, $sIDAction, $sIDForm, $clStructElem, $withGhost, $withBtnOrder, $withOrderActive, $exports, $imports, $tabSort);
        $this->m_clNbOfChart->setDefaultDisplayMode($clXMLReponseWS->sGetDefaultDisplayMode());
        $this->m_clNbOfChart->setTabPossibleDisplayMode($clXMLReponseWS->GetTabPossibleDisplayMode());

        // Paramètres
        $this->m_clNbOfChart->setParam($this->m_clParserParam->getRecordFromID($sIDFormAction, $sIDAction));
        $this->m_clNbOfChart->setNbChart($clXMLReponseWS->nGetNumberOfChart());
    }

    /**
     * @return NumberOfChart|null
     */
    public function getNumberOfChart() : ?NumberOfChart
    {
        return $this->m_clNbOfChart;
    }
}