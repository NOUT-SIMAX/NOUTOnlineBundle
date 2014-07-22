<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 18:09
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

class Form
{
	protected $m_nID;
	protected $m_sTitle;

	public function __construct($nID, $sTitle)
	{
		$this->m_nID = (string)$nID;
		$this->m_sTitle = (string)$sTitle;
	}

	/**
	 * @return mixed
	 */
	public function getTitle()
	{
		return $this->m_sTitle;
	}

	/**
	 * @return mixed
	 */
	public function getID()
	{
		return $this->m_nID;
	}
}
