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
	 * @var string|array
	 */
	protected $m_ValeurRestriction;

	/**
	 * @var string|array
	 */
	protected $m_IconRestriction;

	public function __construct()
	{
		$this->m_sTypeRestriction  = '';
		$this->m_ValeurRestriction = '';
	}

	/**
	 * @param string $ValeurRestriction
	 * @return $this
	 */
	public function setValeurRestriction($ValeurRestriction, $Icon=null)
	{
		$this->m_ValeurRestriction = $ValeurRestriction;
		if (!empty($Icon))
		{
			$this->m_IconRestriction = $Icon;
		}
		return $this;
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $icon
	 * @return $this
	 */
	public function addValeurRestriction($key, $value, $icon=null)
	{
		$this->m_ValeurRestriction[$key] = $value;
		if (!empty($icon))
		{
			$this->m_IconRestriction[$key] = $icon;
		}
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
	 * @return string|array
	 */
	public function getIconRestriction()
	{
		return $this->m_IconRestriction;
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

    /**
     * @param $type
     * @return bool
     */
    public function isTypeRestriction($type)
    {
        return $type==$this->m_sTypeRestriction;
    }

	const R_MAXLENGTH   = 'maxLength';
	const R_ENUMERATION = 'enumeration';
}
