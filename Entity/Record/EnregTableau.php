<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 29/08/14
 * Time: 11:14
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class EnregTableau
{
	public $m_nIDEnreg;
	public $m_nIDTableau;

	public function __construct($nIDTableau='', $nIDEnreg='')
	{
		$this->m_nIDEnreg = $nIDEnreg;
		$this->m_nIDTableau = $nIDTableau;
	}

	/**
	 * @param mixed $m_nIDEnreg
	 */
	public function setIDEnreg($nIDEnreg)
	{
		$this->m_nIDEnreg = $nIDEnreg;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIDEnreg()
	{
		return $this->m_nIDEnreg;
	}

	/**
	 * @param mixed $m_nIDTableau
	 */
	public function setIDTableau($nIDTableau)
	{
		$this->m_nIDTableau = $nIDTableau;
		return $this;
	}

	/**
	 * @return mixed
	 */
	public function getIDTableau()
	{
		return $this->m_nIDTableau;
	}


} 