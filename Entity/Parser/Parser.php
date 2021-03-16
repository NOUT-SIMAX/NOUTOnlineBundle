<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

abstract class Parser extends AbstractParser
{
    abstract public function Parse(XMLResponseWS $clXMLReponseWS);
}