<?php

/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 16:13
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

class Condition extends SOAPParameter
{
    /** @var  CondColumn */
    public $CondCol;
    /** @var  CondType */
    public $CondType;
    /** @var  CondValue */
    public $CondValue;

    /**
     * Condition constructor.
     * @param CondColumn $condColumn
     * @param CondType   $condType
     * @param CondValue  $condValue
     */
    public function __construct(CondColumn $condColumn, CondType $condType, CondValue $condValue)
    {
        $this->CondType = $condType;
        $this->CondCol = $condColumn;
        $this->CondValue = $condValue;
    }

    /**
     * @return string
     */
    public function getClosingTag(): string
    {
        return '</Condition>';
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $content = $this->CondCol->sToSoap();
        $content .= $this->CondType->sToSoap();
        $content .= $this->CondValue->sToSoap();
        return $content;
    }

    /**
     * @return string
     */
    public function getOpeningTag(): string
    {
        return '<Condition>';
    }
}