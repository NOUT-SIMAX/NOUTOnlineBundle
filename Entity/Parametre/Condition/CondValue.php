<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 16:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

class CondValue extends SOAPParameter
{
    /** @var  string $value */
    public $value;

    /**
     * CondValue constructor.
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = (string)$value;
    }

    /**
     * @return string
     */
    public function getOpeningTag(): string
    {
        return '<CondValue>';
    }

    /**
     * @return string
     */
    public function getClosingTag(): string
    {
        return '</CondValue>';
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->value;
    }
}