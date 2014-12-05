<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 15:30
 */

namespace NOUT\Bundle\ContextesBundle\Entity\Menu;


class Menu extends OptionMenu
{
	protected $m_TabOptionMenu;

	public function __construct($sIDOptionMenu, $sLibelle, $sIDMenu)
	{
		parent::__construct($sIDOptionMenu, $sLibelle, $sIDMenu);

		$this->m_TabOptionMenu = array();
	}

	/**
	 * @return bool
	 */
	public function bEstSeparateur()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function bEstMenu()
	{
		return true;
	}

	/**
	 * @return bool
	 */
	public function bRoot()
	{
		return empty($this->m_sIDMenuParent);
	}

	/**
	 * @param array $TabOptionMenu
	 */
	public function setTabOptionMenu($TabOptionMenu)
	{
		$this->m_TabOptionMenu = $TabOptionMenu;
		return $this;
	}

	/**
	 * @return array
	 */
	public function getTabOptionMenu()
	{
		return $this->m_TabOptionMenu;
	}

	/**
	 * @param OptionMenu $oOption
	 * @return $this
	 */
	public function AddOptionMenu(OptionMenu $oOption)
	{
		$this->m_TabOptionMenu[]=$oOption;
		return $this;
	}

	/**
	 * @param OptionMenu $oOption
	 * @param $index
	 * @return $this
	 */
	public function SetOptionMenuAt(OptionMenu $oOption, $index)
	{
		$this->m_TabOptionMenu[$index]=$oOption;
		return $this;
	}

	/**
	 * @param $index
	 * @return $this
	 */
	public function RemoveOptionMenuAt($index)
	{
		unset($this->m_TabOptionMenu[$index]);
		return $this;
	}

	/**
	 * @return $this
	 */
	public function RemoveAll()
	{
		$this->m_TabOptionMenu=array();
		return $this;
	}

	/**
	 * true si contient au moins une option de menu qui n'est pas un séparateur
	 * @return bool
	 */
	public function bIsEmpty()
	{
		foreach($this->m_TabOptionMenu as $clOption)
		{
			if (!$clOption->bEstSeparateur())
				return false;
		}
		return true;
	}

	/**
	 * @return bool
	 */
	public function bLastOptionIsSeparateur()
	{
		if (empty($this->m_TabOptionMenu))
			return false;

		return end($this->m_TabOptionMenu)->bEstSeparateur();
	}

	/**
	 * supprime la dernière option de menu
	 * @return $this
	 */
	public function RemoveLastOption()
	{
		array_pop($this->m_TabOptionMenu);
		return $this;
	}


	/**
	 * @return bool
	 */
	public function bOptionsWithIcon()
	{
		foreach($this->m_TabOptionMenu as $clOption)
		{
			if (!$clOption->bAvecIcon())
				return true;
		}
		return false;
	}

} 