<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 01/10/14
 * Time: 15:40
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau;
use \NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\SetOrderSubList as WSDLOrderSubList;

class SetOrderSubList extends WSDLOrderSubList
{
	public function __construct($nIDColonne, $TabIDEnreg, $nOffset)
	{
		$this->column =$nIDColonne;
		$this->offset = $nOffset;
		$this->items='';

		foreach($TabIDEnreg as $IDEnreg)
		{
			if (strlen($this->items)>0)
				$this->items.='|';

			if ($IDEnreg instanceof EnregTableau)
				$this->items.=$IDEnreg->getIDEnreg();
			else
				$this->items.=$IDEnreg;
		}
	}
} 