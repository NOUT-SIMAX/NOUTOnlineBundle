<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:07
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\CondListType;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;

class UniqueCondListType extends CondListType
{
    /** @var  Condition */
    public $Condition;

    /**
     * UniqueCondListType constructor.
     * @param Condition $condition
     */
    public function __construct(Condition $condition)
    {
        $this->Condition = $condition;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->Condition->sToSOAP();
    }
}