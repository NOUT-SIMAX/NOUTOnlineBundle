<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 01/10/14
 * Time: 15:40
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau;
use \NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderSubList as WSDLReorderSubList;

class ReorderSubList extends WSDLReorderSubList
{
	public function __construct($nIDColonne, $TabIDEnreg, $nScale, $sTypeMove)
	{
		$this->column = $nIDColonne;
		$this->moveType = $sTypeMove;
		$this->scale = $nScale;
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