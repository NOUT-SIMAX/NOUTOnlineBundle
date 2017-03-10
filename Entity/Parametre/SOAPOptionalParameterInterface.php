<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:47
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;


interface SOAPOptionalParameterInterface extends SOAPParemeterInterface
{
    /** string */
    public function getLoneTag();
}