<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:18
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class GetChart
{
	public $Height; // integer
	public $Width; // integer
	public $DPI; // integer
	public $Index; // integer
	public $ParamXML; // string
	public $Table; // string
	public $Axes; // string
	public $Calculation; // CalculationTypeEnum
	public $OnlyData; // integer
}
//***