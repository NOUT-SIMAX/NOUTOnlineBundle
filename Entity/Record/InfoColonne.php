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
	public $m_nIDColonne;
	public $m_Valeur;

	//mise en forme
	public $m_bBold;
	public $m_bItalic;
	public $m_Color;
	public $m_BackgroundColor;

	//controle d'etat de champ
	public $m_bHidden;
	public $m_bDisabled;
	public $m_bReadOnly;


	public function __construct($TabAttrib, $tabAttribLayout)
	{
		$this->m_nIDColonne=0;
		$this->m_Valeur='';

		$this->m_bBold=0;
		$this->m_bItalic=0;
		$this->m_Color=null;
		$this->m_BackgroundColor=null;

		$this->m_bHidden=0;
		$this->m_bDisabled=0;
		$this->m_bReadOnly=0;

		$this->InitInfoColonne($TabAttrib);
		$this->InitInfoColonne($tabAttribLayout);

	}

	public function InitInfoColonne($TabAttrib)
	{
		foreach($TabAttrib as $sName=>$ndAttrib)
		{
			switch($sName)
			{
				case 'bold':
					$this->m_bBold = (int)$ndAttrib;
					break;
				case 'italic':
					$this->m_bItalic = (int)$ndAttrib;
					break;
				case 'hidden':
					$this->m_bHidden = (int)$ndAttrib;
					break;
			}
		}
	}
} 