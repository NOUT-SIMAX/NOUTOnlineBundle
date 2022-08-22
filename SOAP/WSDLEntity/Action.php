<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:39
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecter sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

/**
 * Class Action
 * @package NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity
 */
class Action
{
	public $_; // string
	public $title; // string
	public $typeaction; // string
	public $typereturn; // string
}
//***
