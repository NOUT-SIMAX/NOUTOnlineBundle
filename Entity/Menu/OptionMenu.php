<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 15:30
 *
 * Règle pour l'execution de l'action
 * 1 - Commande sinon vide
 * 2 - IDAction si != 0
 * 3 - Libelle de l'option
 */

namespace NOUT\Bundle\ContextesBundle\Entity\Menu;

class OptionMenu
{
	protected $m_sIDOptionMenu;

	/**
	 * libellé de l'option de menu
	 * @var string
	 */
	protected $m_sLibelle;

	/**
	 * identifiant de l'action à lancer
	 * @var string
	 */
	protected $m_sIDAction;

	/**
	 * commande de l'action
	 * @var string
	 */
	protected $m_sCommande;

	/**
	 * identifiant de l'icone
	 * @var string
	 */
	protected $m_sIDIcone;

	/**
	 * identifiant du menu conteneur
	 * @var string
	 */
	protected $m_sIDMenuParent;

	public function __construct($sIDOptionMenu, $sLibelle, $sIDMenu)
	{
		$this->m_sIDOptionMenu = $sIDOptionMenu;
		$this->m_sLibelle = str_replace('&&', '&', $sLibelle);
		$this->m_sIDAction = null;
		$this->m_sIDIcone = null;
		$this->m_sCommande = null;
		$this->m_sIDMenuParent = $sIDMenu;
	}

	/**
	 * @return bool
	 */
	public function bEstSeparateur()
	{
		return (empty($this->m_sIDAction) && empty($this->m_sCommande) && ($this->m_sLibelle=='-'));
	}

	/**
	 * @return bool
	 */
	public function bEstMenu()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function bAvecIcon()
	{
		return !empty($this->m_sIDIcone);
	}

	/**
	 * @return bool
	 */
	public function bRoot()
	{
		return false;
	}

	/**
	 * @return bool
	 */
	public function bOptionsWithIcon()
	{
		return false;
	}

	/**
	 * @param string $m_sIDOptionMenu
	 * @return $this;
	 */
	public function setIDOptionMenu($sIDOptionMenu)
	{
		$this->m_sIDOptionMenu = $sIDOptionMenu;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDOptionMenu()
	{
		return $this->m_sIDOptionMenu;
	}



	/**
	 * @param string $sCommande
	 */
	public function setCommande($sCommande)
	{
		$this->m_sCommande = $sCommande;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getCommande()
	{
		return $this->sCommande;
	}

	/**
	 * @param string $sIDAction
	 */
	public function setIDAction($sIDAction)
	{
		$this->m_sIDAction = $sIDAction;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDAction()
	{
		return $this->m_sIDAction;
	}

	/**
	/*
	 * Règle pour l'execution de l'action
	 * 1 - Commande sinon vide
	 * 2 - IDAction si != 0
	 * 3 - Libelle de l'option
	 * @return bool
	 */
	public function bExecByIDAction()
	{
		if (!empty($this->m_sCommande))
			return false;

		return !empty($this->m_sIDAction);
	}

	/**
	 * retourne la phrase a executer
	 * @return string
	 */
	public function sGetCommandeToExec()
	{
		if (!empty($this->m_sCommande))
			return $this->m_sCommande;

		return $this->m_sLibelle;
	}

	/**
	 * @param string $sIDIcone
	 */
	public function setIDIcone($sIDIcone)
	{
		$this->m_sIDIcone = $sIDIcone;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDIcone()
	{
		return $this->m_sIDIcone;
	}

	/**
	 * @param string $sIDMenu
	 */
	public function setIDMenuParent($sIDMenu)
	{
		$this->m_sIDMenuParent = $sIDMenu;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getIDMenuParent()
	{
		return $this->m_sIDMenuParent;
	}

	/**
	 * @param string $sLibelle
	 */
	public function setLibelle($sLibelle)
	{
		$this->m_sLibelle = $sLibelle;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}



} 