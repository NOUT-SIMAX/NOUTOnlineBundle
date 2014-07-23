<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:16
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class SpecialParamListType
{
	public $First; // integer
	public $Length; // integer
	public $WithBreakRow; // integer
	public $WithEndCalculation; // integer
	public $ChangePage; // integer
	public $Sort1; // SortType
	public $Sort2; // SortType
	public $Sort3; // SortType
}
//***