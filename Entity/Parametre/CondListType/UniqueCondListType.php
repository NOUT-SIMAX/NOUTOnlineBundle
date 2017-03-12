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
    protected $condition;

    /**
     * UniqueCondListType constructor.
     * @param Condition $condition
     */
    public function __construct($condition)
    {
        $this->condition = $condition;
    }

    public function getContent()
    {
        return $this->condition->sToSOAP();
    }
}