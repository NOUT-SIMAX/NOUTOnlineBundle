<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPOptionalParameterInterface;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

abstract class CondListType extends SOAPParameter implements SOAPOptionalParameterInterface
{
    /** @inheritdoc */
    public function getOpeningTag()
    {
        return '<CondList>';
    }

    /** @inheritdoc */
    public function getClosingTag()
    {
        return '</CondList>';
    }

    public function getLoneTag()
    {
        return '<CondList/>';
    }
}