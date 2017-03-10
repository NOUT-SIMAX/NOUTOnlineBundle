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
    protected $condColumn;
    /** @var  CondType */
    protected $condType;
    /** @var  CondValue */
    protected $condValue;

    /**
     * Condition constructor.
     * @param CondColumn $condColumn
     * @param CondType $condType
     * @param CondValue $condValue
     */
    public function __construct($condColumn, $condType, $condValue)
    {
        $this->condType = $condType;
        $this->condColumn = $condColumn;
        $this->condValue = $condValue;
    }

    public function getClosingTag()
    {
        return '</Condition>';
    }

    public function getContent()
    {
        $content = $this->condColumn->sToSoap();
        $content .= $this->condType->sToSoap();
        $content .= $this->condValue->sToSoap();
        return $content;
    }

    public function getOpeningTag()
    {
        return '<Condition>';
    }
}