<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 04/08/14
 * Time: 16:55
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

class ColonneRestriction
{
	/**
	 * @var string
	 */
	protected $m_sTypeRestriction;
	/**
	 * @var string
	 */
	protected $m_ValeurRestriction;

	public function __construct()
	{
		$this->m_sTypeRestriction  = '';
		$this->m_ValeurRestriction = '';
	}

	/**
	 * @param string $ValeurRestriction
	 * @return $this
	 */
	public function setValeurRestriction($ValeurRestriction)
	{
		$this->m_ValeurRestriction = $ValeurRestriction;

		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 * @return $this
	 */
	public function addValeurRestriction($key, $value)
	{
		$this->m_ValeurRestriction[$key] = $value;

		return $this;
	}

	/**
	 * @return string|array
	 */
	public function getValeurRestriction()
	{
		return $this->m_ValeurRestriction;
	}


	/**
	 * @param string $sTypeRestriction
	 * @return $this
	 */
	public function setTypeRestriction($sTypeRestriction)
	{
		$this->m_sTypeRestriction = $sTypeRestriction;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getTypeRestriction()
	{
		return $this->m_sTypeRestriction;
	}

	const R_MAXLENGTH   = 'maxLength';
	const R_ENUMERATION = 'enumeration';
}
