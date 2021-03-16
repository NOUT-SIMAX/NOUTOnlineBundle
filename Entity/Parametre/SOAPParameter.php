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
    public function sToSoap(): string
    {
        $xml = $this->getOpeningTag();
        $xml .= $this->getContent();
        $xml .= $this->getClosingTag();
        return $xml;
    }

    /**
     * NUSOAP client will try to get the parameter content by converting it to string
     * @return string
     */
    public function __toString(): string
    {
        return $this->getContent();
    }
}