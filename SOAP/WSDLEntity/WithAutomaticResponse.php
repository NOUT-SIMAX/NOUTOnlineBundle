<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 11:00
 */
//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class WithAutomaticResponse
{
	public $IDMessage; // string
	public $ResponseType; // integer
	public $SendAutomaticMessage; // integer
}
//***
