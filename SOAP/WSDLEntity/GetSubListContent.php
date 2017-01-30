<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/01/2017
 * Time: 11:47
 */
//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------


class GetSubListContent
{
	public $Column; // string
	public $Record; // string
    /** @var  SpecialParamListType */
    public $SpecialParamList;
    public $DisplayMode; // DisplayModeParamEnum
}
//***
