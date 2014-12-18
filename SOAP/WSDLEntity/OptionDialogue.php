<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:38
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class OptionDialogue
{
	public $Readable; // integer
	public $EncodingOutput; // integer
	public $ReturnValue; // integer
	public $ReturnXSD; // integer
	public $HTTPForceReturn; // integer
	public $Ghost; // integer
	public $DefaultPagination; // integer
	public $DisplayValue; // integer
	public $LanguageCode; // integer
	public $WithFieldStateControl; // integer
	public $ListContentAsync; // integer
}
//***
