<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/07/14
 * Time: 18:01
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity\ReponseWebService;

/**
 * Class ConnectedUser
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 * Utilisateur connecté
 */
class ConnectedUser
{
	protected $m_nUser;
	protected $m_sUserTitle;
	protected $m_nFormID;
	protected $m_sFormTitle;

	/**
	 * @param $nUserID : identifiant de l'utilisateur
	 * @param $sUserTitle : minidesc de l'utilisateur
	 * @param $nFormID : identifiant du tableau réel de l'utilisateur
	 * @param $sFormTitle : minidesc du tableau réel de l'utilisateur
	 */
	public function __construct($nUserID, $sUserTitle, $nFormID, $sFormTitle)
	{
		$this->m_nUserID = (string)$nUserID;
		$this->m_sUserTitle = (string)$sUserTitle;
		$this->m_nFormID = (string)$nFormID;
		$this->m_sFormTitle = (string)$sFormTitle;
	}

	/**
	 * @return string
	 */
	public function getFormID()
	{
		return $this->m_nFormID;
	}

	/**
	 * @return string
	 */
	public function getUserID()
	{
		return $this->m_nUserID;
	}

	/**
	 * @return string
	 */
	public function getFormTitle()
	{
		return $this->m_sFormTitle;
	}

	/**
	 * @return string
	 */
	public function getUserTitle()
	{
		return $this->m_sUserTitle;
	}


} 