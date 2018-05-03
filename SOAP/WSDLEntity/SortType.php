<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:16
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class SortType implements SerializableEntity
{
	public $_; // string
	public $asc; // string

    static function getAttributes()
    {
        return array(
            'asc'
        );
    }

    static function getValueType()
    {
        return 'string';
    }

    static function getEntityDefinition()
    {
        return new WSDLEntityDefinition(self::getAttributes(), self::getValueType());
    }
}
//***
