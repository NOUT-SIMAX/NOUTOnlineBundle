<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 17:17
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator;


class AndOperator extends Operator
{
    public function __construct()
    {
        $this->type = OperatorType::OP_AND;
    }
}