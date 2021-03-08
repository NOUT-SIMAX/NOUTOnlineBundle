<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator\Operator;

class MultipleCondListType extends CondListType
{
    /** @var  Operator $operator */
    public $Operator;

    /**
     * MultipleCondListType constructor.
     * @param Operator $operator
     */
    public function __construct(Operator $operator)
    {
        $this->Operator = $operator;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->Operator->sToSoap();
    }
}