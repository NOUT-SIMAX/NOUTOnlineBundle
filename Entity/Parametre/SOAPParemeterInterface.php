<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:41
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;


interface SOAPParemeterInterface
{
    /** @return string */
    public function sToSoap(): string;

    /** @return string */
    public function getOpeningTag(): string;

    /** @return string */
    public function getClosingTag(): string;

    /** @return string */
    public function getContent(): string;
}