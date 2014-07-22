<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:13
 */
//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------


class GetColInRecord
{
	public $Column; // string
	public $Record; // string
	public $Encoding; // string
	public $MimeType; // string
	public $TransColor; // string
	public $WantContent; // string
	public $ColorFrom; // string
	public $ColorTo; // string
	public $Width; // string
	public $Height; // string
}
//***