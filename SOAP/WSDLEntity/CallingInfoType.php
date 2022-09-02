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

class CallingInfoType
{
    /** @var string */
	public $Column;
    /** @var string */
	public $Context;
    /** @var string */
    public $Record;
    /** @var string */
    public $Value;

    /** @var null|SelectedItemsType|SelectedItemsType[] */
    public $SelectedItems;
}
//***
