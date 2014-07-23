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
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class Form
{
	public $_; // string
	public $title; // string
	public $checksum; // string
	public $typeform; // string
	public $withBtnOrderPossible; // integer
	public $sort1; // string
	public $sort2; // string
	public $sort3; // string
	public $sort1asc; // string
	public $sort2asc; // string
	public $sort3asc; // string
}
//***