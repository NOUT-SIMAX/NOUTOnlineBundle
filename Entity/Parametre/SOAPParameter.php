<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:50
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;


abstract class SOAPParameter implements SOAPParemeterInterface
{
    /** @inheritdoc */
    public function sToSoap()
    {
        $xml = $this->getOpeningTag();
        $xml .= $this->getContent();
        $xml .= $this->getClosingTag();
        return $xml;
    }

    public function __toString()
    {
        return $this->getContent();
    }
}