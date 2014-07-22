<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:29
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------
class FilterType
{
	public $Way; // WayEnum
	public $State; // StateEnum
	public $Inner; // integer
	public $Email; // integer
	public $Spam; // integer
	public $Max; // integer
	public $From; // string
	public $Containing; // string
}
//***