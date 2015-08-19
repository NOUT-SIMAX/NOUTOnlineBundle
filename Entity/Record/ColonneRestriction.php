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
	}

	/**
	 * @param string $Type
     * @param string $Valeur
	 * @return $this
	 */
	public function addRestrictionSimple($Type, $Valeur)
	{
        $this->m_TabRestriction[$Type]=$Valeur;
	}

	/**
	 * @param $key
	 * @param $value
	 * @param $icon
	 * @return $this
	 */
	public function addRestrictionArray($Type, $key, $value, $icon=null)
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
	 * @return string|array
	 */
	public function getIconRestriction($Type)
	{
        if (isset($this->m_IconRestriction[$Type]))
        {
            return $this->m_IconRestriction[$Type];
        }

		return null;
	}

	/**
	 * @return string
	 */
	public function getRestriction($type)
	{
        if (isset($this->m_TabRestriction[$type]))
        {
            return $this->m_TabRestriction[$type];
        }

        return null;
	}

    /**
     * @param $type
     * @return bool
     */
    public function hasTypeRestriction($type)
    {
        return isset($this->m_TabRestriction[$type]);
    }

	const R_MAXLENGTH   = 'maxLength';
	const R_ENUMERATION = 'enumeration';
    const R_LENGTH      = 'length';
}
