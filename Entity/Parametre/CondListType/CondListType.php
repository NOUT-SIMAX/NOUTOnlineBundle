<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParemeterInterface;

abstract class CondListType extends SOAPParameter implements SOAPParemeterInterface
{
    /** @inheritdoc */
    public function getOpeningTag(): string
    {
        return '<CondList>';
    }

    /** @inheritdoc */
    public function getClosingTag(): string
    {
        return '</CondList>';
    }
}