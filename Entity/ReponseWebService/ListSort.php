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
	public $m_nIDColonnne;
	/**
	 * @var bool
	 * si le tri est ascendant ou descendant
	 */
	public $m_bAsc;

	public function __construct($sID, $bAsc)
	{
		$this->m_nIDColonnne = (string) $sID;
		$this->m_bAsc        = ((int) $bAsc == 1) ? true : false;
	}
}
