<?php
/**
 * Created by PhpStorm.
 * User: Ninon <ninon@nout.fr>
 * Date: 18/07/14
 * Time: 18:09
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

class Form
{
	/**
	 * @var string
	 * identifiant du formulaire
	 */
	public $m_nID;

	/**
	 * @var string
	 * titre du formulaire
	 */
	public $m_sTitle;
	/**
	 * @var bool
	 * si il est possible d'ordonner la liste
	 */
	public $m_bWithBtnOrderPossible;
	/**
	 * @var array
	 * tableau des tris appliqués à la liste
	 */
	public $m_TabSort;

	public function __construct($nID, $sTitle, $withBtnOrderPossible)
	{
		$this->m_nID                   = (string) $nID;
		$this->m_sTitle                = (string) $sTitle;
		$this->m_bWithBtnOrderPossible = !!$withBtnOrderPossible;
		$this->m_TabSort               = array();
	}

	/**
	 * @return string
	 */
	public function getTitle()
	{
		return $this->m_sTitle;
	}

	/**
	 * @return string
	 */
	public function getID()
	{
		return $this->m_nID;
	}

    public function hasOrderBtn() {
        return $this->m_bWithBtnOrderPossible;
    }
}
