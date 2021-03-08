<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 16:31
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class ListSort
{
	/**
	 * @var string
	 * identifiant de la colonne de tri
	 */
	public $idcolumn;
	/**
	 * @var bool
	 * si le tri est ascendant ou descendant
	 */
	public $asc;

	public function __construct($sID, $bAsc)
	{
		$this->idcolumn = (string) $sID;
		$this->asc      = ((int) $bAsc == 1);
	}
}
