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
	public $m_nIDEnreg='';

	/**
	 * @var string
	 */
	public $m_nIDTableau='';

	public function __construct($nIDTableau = '', $nIDEnreg = '')
	{
		$this->m_nIDEnreg   = (string) $nIDEnreg;
		$this->m_nIDTableau = (string) $nIDTableau;
	}

	/**
	 * @param $nIDEnreg
     * @return $this
	 */
	public function setIDEnreg($nIDEnreg): EnregTableau
    {
		$this->m_nIDEnreg = (string) $nIDEnreg;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDEnreg(): string
    {
		return $this->m_nIDEnreg;
	}

	/**
	 * @param $nIDTableau
     * @return $this
	 */
	public function setIDTableau($nIDTableau): EnregTableau
    {
		$this->m_nIDTableau = (string) $nIDTableau;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDTableau(): string
    {
		return $this->m_nIDTableau;
	}
}
