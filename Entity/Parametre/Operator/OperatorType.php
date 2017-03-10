<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 16:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator;


abstract class OperatorType
{
    const OP_AND = 'AND';
    const OP_OR  = 'OR';
    const OP_NOT = 'NOT';
}