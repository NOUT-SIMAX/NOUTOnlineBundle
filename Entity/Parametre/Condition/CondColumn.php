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
    protected $column;

    public function __construct($column)
    {
        $this->column = $column;
    }

    public function getOpeningTag()
    {
        return '<CondCol>';
    }

    public function getClosingTag()
    {
        return '</CondCol>';
    }

    public function getContent()
    {
        return $this->column;
    }
}