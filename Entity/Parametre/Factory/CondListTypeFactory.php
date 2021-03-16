<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:08
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Factory;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType\CondListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType\MultipleCondListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType\UniqueCondListType;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

abstract class CondListTypeFactory
{
    /**
     * @param SOAPParameter $param
     * @return CondListType
     */
    public static function create(SOAPParameter $param){
        if($param instanceof Condition)
            return new UniqueCondListType($param);
        if($param instanceof Operator)
        {
            return new MultipleCondListType($param);
        }
        else
            throw new \InvalidArgumentException('Type ' . get_class($param) . ' not supported for Condlisttype');
    }
}