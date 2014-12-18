<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 17:30
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderList as WSDLOrderList;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau;

class SetOrderList extends WSDLOrderList
{
	public function __construct($TabIDEnreg, $nOffset)
	{
		$this->offset = $nOffset;
		$this->items  = '';

		foreach ($TabIDEnreg as $IDEnreg)
		{
			if (!empty($this->items))
			{
				$this->items .= '|';
			}

			if ($IDEnreg instanceof EnregTableau)
			{
				$this->items .= $IDEnreg->getIDEnreg();
			}
			else
			{
				$this->items .= $IDEnreg;
			}
		}
	}
}
