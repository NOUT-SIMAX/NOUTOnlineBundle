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
class ListParams
{
	public $Table; // string
	public $ParamXML; // string
	public $SpecialParamList; // SpecialParamListType
	public $CallingColumn; // string
	public $NoCache; // integer
	public $Checksum; // integer
	public $DisplayMode; // DisplayModeParamEnum
}
//***
