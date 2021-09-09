<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 07/08/14
 * Time: 14:58
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/*
    <FolderCount>
      <NbUnRead>1</NbUnRead>
      <NbReceive>9</NbReceive>
    </FolderCount>
*/
class FolderCount
{
	public $m_nNbUnRead;
	public $m_nNbReceive;

	public function __construct()
	{
		$this->m_nNbUnRead = 0;
		$this->m_nNbReceive = 0;
	}
}
