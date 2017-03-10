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
    protected $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function getOpeningTag()
    {
        return '<CondValue>';
    }

    public function getClosingTag()
    {
        return '</CondValue>';
    }

    public function getContent()
    {
        return $this->value;
    }
}