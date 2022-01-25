<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:27
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------

class GetListIDMessFromRequest
{
    public $StartDate; // string
    public $EndDate; // string
    public $Filter; // FilterType
    public $UserMessagerie; // string
    public $Sort1; 				// SortType
    public $Sort2; 				// SortType
    public $Sort3; 				// SortType
}
//***
