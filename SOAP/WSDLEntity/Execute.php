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

class Execute implements SerializableEntity
{
	public $ID; 				// string
	public $Sentence; 			// string
	public $ParamXML; 			// string
	public $SpecialParamList; 	// SpecialParamListType
	public $Checksum; 			// integer
    public $CallingColumn; 		// string
    public $CallingInfo; 		// CallingInfoType
	public $DisplayMode; 		// DisplayModeParamEnum
    public $BtnListMode;        // integer
    public $Final;              // integer

    /** @var null|SelectedItemsType|SelectedItemsType[] */
    public $SelectedItems;      // SelectedItems

    static function getAttributes() {
        return array();
    }

    static function getValueType() {
        return array(
            'DisplayMode'       => 'string',
            'Sentence'          => 'string',
            'ParamXML'          => 'string',
            'CallingColumn'     => 'string',
            'ID'                => 'string',
            'Final'             => 'integer',
            'SpecialParamList'  => SpecialParamListType::getEntityDefinition(),
            'SelectedItems'     => SelectedItemsType::getEntityDefinition(),
        );
    }

    static function getEntityDefinition()
    {
        return new WSDLEntityDefinition(self::getAttributes(), self::getValueType());
    }
}
//***
