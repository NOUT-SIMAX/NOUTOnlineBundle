<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:12
 */
//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------


class GetPlanningInfo
{
	public $Resource; // string
	public $StartTime; // string
	public $EndTime; // string
	public $Table; // string
	public $ID; // string
	public $ParamXML; // string
}
//***