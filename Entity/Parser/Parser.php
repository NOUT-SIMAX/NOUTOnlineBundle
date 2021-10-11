<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

abstract class Parser extends AbstractParser
{
    /**
     * @param XMLResponseWS $clXMLReponseWS
     * @param               $idForm
     * @return mixed
     */
    abstract public function Parse(XMLResponseWS $clXMLReponseWS, $idForm);
}