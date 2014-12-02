<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 17:46
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Parametre;

use NOUT\Bundle\NOUTOnlineBundle\Entity\Record\EnregTableau;
use \NOUT\Bundle\NOUTOnlineBundle\SOAP\WSDLEntity\ReorderList as WSDLReorderList;

class ReorderList extends WSDLReorderList
{
	//type de déplacement
	const MOVE_FIRST    = 'First';  // place les éléments sélectionnés en premier
	const MOVE_UP       = 'Up';     // conjuré avec Scale permet de monter les éléments de x position
	const MOVE_DOWN     = 'Down';   // conjuré avec Scale permet de descendre les éléments de x position
	const MOVE_LAST     = 'Last';   // place les éléments sélectionnés en fin de liste



	public function __construct($TabIDEnreg, $nScale, $sTypeMove)
	{
		$this->moveType = $sTypeMove;
		$this->scale = $nScale;
		$this->items='';

		foreach($TabIDEnreg as $IDEnreg)
		{
			if (!empty($this->items))
				$this->items.='|';

			if ($IDEnreg instanceof EnregTableau)
				$this->items.=$IDEnreg->getIDEnreg();
			else
				$this->items.=$IDEnreg;
		}
	}

} 