<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 16:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

class CondColumn extends SOAPParameter
{
    /** @var  string $column */
    public $Column;

    /**
     * CondColumn constructor.
     * @param $column
     */
    public function __construct($column)
    {
        $this->Column = (string)$column;
    }

    /**
     * @return string
     */
    public function getOpeningTag(): string
    {
        return '<CondCol>';
    }

    /**
     * @return string
     */
    public function getClosingTag(): string
    {
        return '</CondCol>';
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->Column;
    }
}