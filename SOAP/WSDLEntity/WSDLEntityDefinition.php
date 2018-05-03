<?php
/**
 * Created by PhpStorm.
 * User: simon
 * Date: 30/04/2018
 * Time: 15:54
 */

namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;


class WSDLEntityDefinition
{
    /** @var $attributes array of possible attributes */
    public $attributes;
    /** @var $valueType string|array either a primitive type or an array of primitive types or entity definitions*/
    public $valueType;

    public function __construct($attributes, $valueType) {
        $this->attributes = $attributes;
        $this->valueType = $valueType;
    }
}