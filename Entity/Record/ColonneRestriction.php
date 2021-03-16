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
	 * @var string[]
	 */
	protected $m_TabRestriction;

	/**
	 * @var string|array
	 */
	protected $m_IconRestriction;

	public function __construct()
	{
		$this->m_TabRestriction  = array();
		$this->m_IconRestriction = array();
	}

    /**
     * @param string $Type
     * @param mixed  $Valeur
     * @return $this
     */
	public function addRestrictionSimple(string $Type, $Valeur): ColonneRestriction
    {
        $this->m_TabRestriction[$Type]=$Valeur;
        return $this;
	}

	public  function getRestrictions(): array
    {
	    return $this->m_TabRestriction;
    }

	/**
     * @param $Type
	 * @param $key
	 * @param $value
	 * @param $icon
	 * @return $this
	 */
	public function addRestrictionArray($Type, $key, $value, $icon=null): ColonneRestriction
    {
        if (!isset($this->m_TabRestriction[$Type]))
        {
            $this->m_TabRestriction[$Type] = array();
            $this->m_IconRestriction[$Type] = array();
        }

		$this->m_TabRestriction[$Type][$key] = $value;
		if (!empty($icon))
		{
			$this->m_IconRestriction[$Type][$key] = $icon;
		}
		return $this;
	}

	/**
     * @param $Type
	 * @return string|array
	 */
	public function getIconRestriction($Type)
	{
        if (isset($this->m_IconRestriction[$Type]))
        {
            return $this->m_IconRestriction[$Type];
        }

		return array();
	}

	/**
     * @param $type
	 * @return mixed
	 */
	public function getRestriction($type)
	{
        if (isset($this->m_TabRestriction[$type]))
        {
            return $this->m_TabRestriction[$type];
        }

        return array();
	}

    /**
     * @param $type
     * @return bool
     */
    public function hasTypeRestriction($type): bool
    {
        return isset($this->m_TabRestriction[$type]);
    }

	const R_MAXLENGTH   = 'maxLength';
	const R_ENUMERATION = 'enumeration';
    const R_LENGTH      = 'length';

    const R_NumericDisplay              = 'numericDisplay';
    const ROPTION_Shape                 = 'shape';
    const ROPTION_Size                  = 'size';
    const ROPTION_DisplayValue          = 'displayValue';

    const R_NumericDisplay_Stage        = 'stage';
    const RSTAGEOPTION_Value            = 'value';
    const RSTAGEOPTION_Color            = 'color';
}
