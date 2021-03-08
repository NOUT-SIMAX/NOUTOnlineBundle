<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserNumberOfChart extends ParserWithParam
{
    /** @var int  */
    protected $m_nNbOfChart = 0;

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

        $this->m_nNbOfChart = $clXMLReponseWS->nGetNumberOfChart();
    }

    /**
     * @return int
     */
    public function getNumberOfChart() : int
    {
        return $this->m_nNbOfChart;
    }
}