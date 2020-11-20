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
class UsernameToken
{
    /** @var string */
	public $Username;
	/** @var string */
	public $Password;
	/** @var string */
	public $Nonce;
	/** @var string */
	public $Created;
	/** @var Encryption */
	public $Encryption;
}
//***
