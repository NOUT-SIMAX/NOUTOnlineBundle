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

namespace NOUT\Bundle\ContextsBundle\Entity\Menu;

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

	/**
	 * vrai si c'est un menu, faux si c'est une option de menu
	 * @var bool
	 */
	protected $m_bEstMenu;

	/**
	 * @param $sIDOptionMenu
	 * @param $sLibelle
	 * @param $sIDMenu
	 */
	public function __construct($sIDOptionMenu, $sLibelle, $sIDMenu, $bEstMenu=null)
	{
		$this->m_sIDOptionMenu = $sIDOptionMenu;
		$this->m_sLibelle = str_replace('&&', '&', $sLibelle);
		$this->m_sIDAction = null;
		$this->m_sIDIcone = null;
		$this->m_sCommande = null;
		$this->m_sIDMenuParent = $sIDMenu;
		$this->m_bEstMenu = is_null($bEstMenu) ? false : $bEstMenu;
	}



	/*******************************************
	 * Accesseur get pour la serialization JSON
	 *******************************************/

	/**
	 * @return bool
	 */
	public function getEstMenu()
	{
		return $this->m_bEstMenu;
	}

	/**
	 * @return string
	 */
	public function getIdOptionMenu()
	{
		return $this->m_sIDOptionMenu;
	}

	/**
	 * @return string
	 */
	public function getIdAction()
	{
		return $this->m_sIDAction;
	}

	/**
	 * @return string
	 */
	public function getCommande()
	{
		return $this->m_sCommande;
	}

	/**
	 * @return string
	 */
	public function getIdIcone()
	{
		return $this->m_sIDIcone;
	}

	/**
	 * @return string
	 */
	public function getIdMenuParent()
	{
		return $this->m_sIDMenuParent;
	}

	/**
	 * @return string
	 */
	public function getLibelle()
	{
		return $this->m_sLibelle;
	}

	/*******************************************
	 * Accesseur set pour la serialization JSON
	 *******************************************/

	/**
	 * @param string $sCommande
	 */
	public function setCommande($sCommande)
	{
		$this->m_sCommande = $sCommande;
		return $this;
	}


	/**
	 * @param string $sIDAction
	 */
	public function setIdAction($sIDAction)
	{
		$this->m_sIDAction = $sIDAction;
		return $this;
	}



	/**
	 * @param string $sIDIcone
	 */
	public function setIdIcone($sIDIcone)
	{
		$this->m_sIDIcone = $sIDIcone;
		return $this;
	}

	/**
	 * @param string $sIDMenu
	 */
	public function setIdMenuParent($sIDMenu)
	{
		$this->m_sIDMenuParent = $sIDMenu;
		return $this;
	}

	/**
	 * @param string $sLibelle
	 */
	public function setLibelle($sLibelle)
	{
		$this->m_sLibelle = $sLibelle;
		return $this;
	}

	/***************************************
	 * Autres méthodes
	 ***************************************/

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
	 * @return bool
	 */
	public function bRoot()
	{
		return $this->m_bEstMenu && empty($this->m_sIDMenuParent);
	}

	/**
	 * @return bool
	 */
	public function bEstSeparateur()
	{
		return !$this->m_bEstMenu && (empty($this->m_sIDAction) && empty($this->m_sCommande) && ($this->m_sLibelle=='-'));
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
	public function bOptionsWithIcon()
	{
		return false;
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




} 