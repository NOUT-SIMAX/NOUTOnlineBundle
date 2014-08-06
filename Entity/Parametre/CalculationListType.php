<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 06/08/14
 * Time: 17:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;
use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\CalculationListType as WSDLCalculationListType;

class CalculationListType extends WSDLCalculationListType
{
	const SUM       = 'sum';
	const COUNT     = 'count';
	const MAX       = 'max';
	const MIN       = 'min';
	const AVERAGE   = 'average';
	const PERCENT   = 'percent';

	public function __construct($calculation)
	{
		$this->Calculation = $calculation;
	}
} 