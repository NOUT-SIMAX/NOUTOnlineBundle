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

class ExecuteWithoutIHM implements SerializableEntity
{
    public $ID;                   // string
    public $Sentence;             // string
    public $ParamXML;             // string
    public $BtnListMode;          // integer
    public $Final;                // integer
    public $UpdateData;           // string

    static function getAttributes() {
        return array();
    }

    static function getValueType() {
        return array(
            'Sentence'          => 'string',
            'ParamXML'          => 'string',
            'UpdateData'        => 'string',
            'ID'                => 'string',
            'Final'             => 'integer',
        );
    }

    static function getEntityDefinition()
    {
        return new WSDLEntityDefinition(self::getAttributes(), self::getValueType());
    }
}
//***
