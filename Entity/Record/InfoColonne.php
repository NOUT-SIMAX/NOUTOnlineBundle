<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 31/07/14
 * Time: 14:11
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\Record;

class InfoColonne
{
	protected $m_nIDColonne;

	//mise en forme
	protected $m_bBold;
	protected $m_bItalic;
	protected $m_Color;
	protected $m_BackgroundColor;

	//controle d'etat de champ
	protected $m_bHidden;
	protected $m_bDisabled;
	protected $m_bReadOnly;


	public function __construct($sIDColonne, $TabAttrib, $tabAttribLayout)
	{
		$this->m_nIDColonne = $sIDColonne;

		$this->m_bBold           = 0;
		$this->m_bItalic         = 0;
		$this->m_Color           = null;
		$this->m_BackgroundColor = null;

		$this->m_bHidden   = 0;
		$this->m_bDisabled = 0;
		$this->m_bReadOnly = 0;

		$this->InitInfoColonne($TabAttrib);
		$this->InitInfoColonne($tabAttribLayout);
	}

	public function InitInfoColonne($TabAttrib)
	{
		foreach ($TabAttrib as $sName => $ndAttrib)
		{
			switch ($sName)
			{
			case 'bold':
				$this->m_bBold = (int) $ndAttrib;
				break;
			case 'italic':
				$this->m_bItalic = (int) $ndAttrib;
				break;
			case 'hidden':
				$this->m_bHidden = (int) $ndAttrib;
				break;
			}
		}
	}

	/**
	 * @return null
	 */
	public function getBackgroundColor()
	{
		return $this->m_BackgroundColor;
	}

	/**
	 * @return null
	 */
	public function getColor()
	{
		return $this->m_Color;
	}

	/**
	 * @return int
	 */
	public function getBold()
	{
		return $this->m_bBold;
	}

	/**
	 * @return int
	 */
	public function getDisabled()
	{
		return $this->m_bDisabled;
	}

	/**
	 * @return int
	 */
	public function getHidden()
	{
		return $this->m_bHidden;
	}

	/**
	 * @return int
	 */
	public function getItalic()
	{
		return $this->m_bItalic;
	}

	/**
	 * @return int
	 */
	public function getReadOnly()
	{
		return $this->m_bReadOnly;
	}

	/**
	 * @return int
	 */
	public function getIDColonne()
	{
		return $this->m_nIDColonne;
	}
}
