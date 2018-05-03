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

class SpecialParamListType implements SerializableEntity
{
	public $First; 				// integer
	public $Length; 			// integer
	public $WithBreakRow; 		// integer
	public $WithEndCalculation;	// integer
	public $ChangePage; 		// integer
	public $Sort1; 				// SortType
	public $Sort2; 				// SortType
	public $Sort3; 				// SortType
    public $ItemTreeParent;     // Parent

    public function initFirstLength($first=0, $length=50)
    {
        $this->First = $first;
        $this->Length = $length;
    }

    static function getAttributes() {
        return array();
    }

    static function getValueType()
    {
        return array(
            'First' => 'string',
            'Length' => 'string',
            'WithBreakRow' => 'string',
            'WithEndCalculation' => 'string',
            'ChangePage' => 'string',
            'Sort1' => SortType::getEntityDefinition(),
            'Sort2' => SortType::getEntityDefinition(),
            'Sort3' => SortType::getEntityDefinition()
        );
    }

    static function getEntityDefinition() {
        return new WSDLEntityDefinition(self::getAttributes(), self::getValueType());
    }
}
//***
