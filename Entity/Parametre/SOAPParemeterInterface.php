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
    public function sToSoap();

    /** @return string */
    public function getOpeningTag();

    /** @return string */
    public function getClosingTag();

    /** @return string */
    public function getContent();
}