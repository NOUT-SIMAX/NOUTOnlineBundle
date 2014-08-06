<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 06/08/14
 * Time: 18:11
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ColListType as WSDLColListType;

class ColListType extends WSDLColListType
{
	public function __construct($ColList)
	{
		$this->Col = $ColList;
	}
} 