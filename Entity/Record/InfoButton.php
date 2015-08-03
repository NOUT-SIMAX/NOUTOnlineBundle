<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/08/2015
 * Time: 17:01
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;


class InfoButton 
{

	/**
	 * @var string
	 */
	private $m_eTypeSelection;

	/**
	 * @var string
	 */
	private $m_IDAction;

	/**
	 * @var string|null
	 */
	private $m_IDIcon;

	/**
	 * @var string
	 */
	private $m_nWithValidation;


	public function __construct($selection, $idaction, $icon, $withvalidation)
	{
		$this->m_eTypeSelection=$selection;
		$this->m_IDAction=$idaction;
		$this->m_IDIcon=$icon;
		$this->m_nWithValidation=$withvalidation;
	}

	/**
	 * @return string
	 */
	public function getTypeSelection()
	{
		return $this->m_eTypeSelection;
	}

	/**
	 * @return string
	 */
	public function getIDAction()
	{
		return $this->m_IDAction;
	}

	/**
	 * @return string
	 */
	public function getIDIcon()
	{
		return $this->m_IDIcon;
	}

	/**
	 * @return string
	 */
	public function getWithValidation()
	{
		return $this->m_nWithValidation;
	}
}