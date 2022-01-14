<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parser;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\Record;
use NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService\XMLResponseWS;

class ParserMailRecord extends ParserRecord
{
    /**
     * @param XMLResponseWS $clResponseXML
     * @return null|Record
     */
    public function getRecord(XMLResponseWS $clResponseXML): ?Record
    {
        $tabRecord =$this->m_clParser->getFullCache()->getMapIDTableauIDEnreg2Record();
        if (count($tabRecord)==0){
            return null;
        }
        $key = array_key_first($tabRecord);
        return $tabRecord[$key];
    }
}