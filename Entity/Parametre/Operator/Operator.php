<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 10/03/2017
 * Time: 15:36
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Operator;


use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\Condition\Condition;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\ConditionColonne;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre\SOAPParameter;

abstract class Operator extends SOAPParameter
{
    /** @var  string */
    protected $type;
    /** @var  ConditionColonne[] */
    protected $conditions = array();
    /** @var Operator[] */
    protected $operators = array();

    public function getOpeningTag()
    {
        return '<Operator type="' . $this->type . '">';
    }

    public function getClosingTag()
    {
        return '</Operator>';
    }

    public function getContent()
    {
        $content = '';
        foreach($this->conditions as $condition){
            $content .= $condition->sToSOAP();
        }
        foreach($this->operators as $operator){
            $content .= $operator->sToSoap();
        }
        return $content;
    }

    /**
     * @param Condition $condition
     * @return $this
     */
    public function addCondition($condition){
        array_push($this->conditions, $condition);
        return $this;
    }

    /**
     * @param Operator $operator
     * @return $this
     */
    public function addOperator($operator){
        array_push($this->operators, $operator);
        return $this;
    }
}