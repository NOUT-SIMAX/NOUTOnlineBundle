<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/14
 * Time: 14:11
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class InfoColonne
{
	protected $m_sValeur;

	/**
	 * @param mixed $m_sValeur
	 */
	public function setValeur($sValeur)
	{
		$this->m_sValeur = $sValeur;
	}

	/**
	 * @return mixed
	 */
	public function getValeur()
	{
		return $this->m_sValeur;
	}


} 