<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:41
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------
class PlanningFilter
{
	public $Resource; // string
	public $Table; // string
	public $StartTime; // string
	public $EndTime; // string
}
//***