<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/10/2021
 */



//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class GetRedoListID
{
	public $StartDate; // string
	public $EndDate; // string
    public $DoneBy; // string
    public $ActionType; // string
    public $Form; // string
    public $OtherCriteria; // string
    public $Sort1; 				// SortType
    public $Sort2; 				// SortType
    public $Sort3; 				// SortType
}
//***
