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
	/**
	 * @var string
	 */
	public $m_nIDEnreg;

	/**
	 * @var string
	 */
	public $m_nIDTableau;

	public function __construct($nIDTableau='', $nIDEnreg='')
	{
		$this->m_nIDEnreg = (string)$nIDEnreg;
		$this->m_nIDTableau = (string)$nIDTableau;
	}

	/**
	 * @param string $m_nIDEnreg
	 */
	public function setIDEnreg($nIDEnreg)
	{
		$this->m_nIDEnreg = (string)$nIDEnreg;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDEnreg()
	{
		return $this->m_nIDEnreg;
	}

	/**
	 * @param string $m_nIDTableau
	 */
	public function setIDTableau($nIDTableau)
	{
		$this->m_nIDTableau = (string)$nIDTableau;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDTableau()
	{
		return $this->m_nIDTableau;
	}


} 