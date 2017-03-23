<?php
namespace NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity;

/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 22/07/14
 * Time: 17:20
 */

//-------------------------------------------------------------------------------------------------------------------
// Ensemble de classes utilisé par la classe SimaxOnlineServiceProxy
// Note : les conventions de code peuvent semblé non respecté sur les nom de variables, mais elle corresponde en realite
// au fichier WSDL
//-------------------------------------------------------------------------------------------------------------------
class CalculationListType
{
    /** @var  string[] */
	public $Calculation;

	const SUM       = 'Sum';
	const AVERAGE   = 'Average';
	const MIN       = 'Min';
	const MAX       = 'Max';
	const COUNT     = 'Count';
	const PERCENT   = 'Percent';

    /**
     * @return int[]
     */
	public static function getAll(){
	    return array(
	        self::SUM,
            self::AVERAGE,
            self::MIN,
            self::MAX,
            self::COUNT,
            self::PERCENT,
        );
    }
}
//***
