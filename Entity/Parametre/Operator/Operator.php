<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:36
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParemeterInterface;

class Operator extends SOAPParameter implements SOAPParemeterInterface
{
    const OP_AND = 'AND';
    const OP_OR  = 'OR';
    const OP_NOT = 'NOT';

    /** @var  string */
    public $type;
    /** @var  Condition[] */
    public $Conditions = array();
    /** @var Operator[] */
    protected $Operators = array();

    /**
     * Operator constructor.
     * @param string $type
     */
    public function __construct(string $type)
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getOpeningTag(): string
    {
        return '<Operator type="' . $this->type . '">';
    }

    /**
     * @return string
     */
    public function getClosingTag(): string
    {
        return '</Operator>';
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        $content = '';
        foreach($this->Conditions as $condition){
            $content .= $condition->sToSOAP();
        }
        foreach($this->Operators as $operator){
            $content .= $operator->sToSoap();
        }
        return $content;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addCondition(Condition $condition): Operator
    {
        array_push($this->Conditions, $condition);
        return $this;
    }

    /**
     * @param Operator $operator
     * @return $this
     */
    public function addOperator(Operator $operator): Operator
    {
        array_push($this->Operators, $operator);
        return $this;
    }
}