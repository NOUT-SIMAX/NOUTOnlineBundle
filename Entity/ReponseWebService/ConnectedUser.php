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
    /** @var string  */
	protected $m_nUserID;
	/** @var string  */
	protected $m_sUserTitle;
	/** @var string  */
	protected $m_nFormID;
	/** @var string  */
	protected $m_sFormTitle;

	/** @var string  */
	protected $m_sPseudo='';
	/** @var null|int  */
	protected $m_nKS = null;
	/** @var null|string  */
	protected $m_sPwd = null;
	/** @var null|string  */
	protected $m_sIV = null;
	/** @var bool */
	protected $m_bExtranet = false;

	/**
	 * @param $nUserID : identifiant de l'utilisateur
	 * @param $sUserTitle : minidesc de l'utilisateur
	 * @param $nFormID : identifiant du tableau réel de l'utilisateur
	 * @param $sFormTitle : minidesc du tableau réel de l'utilisateur
	 */
	public function __construct($nUserID, $sUserTitle, $nFormID, $sFormTitle)
	{
		$this->m_nUserID    = (string) $nUserID;
		$this->m_sUserTitle = (string) $sUserTitle;
		$this->m_nFormID    = (string) $nFormID;
		$this->m_sFormTitle = (string) $sFormTitle;
	}

	/**
	 * @return string
	 */
	public function getFormID(): string
    {
		return $this->m_nFormID;
	}

	/**
	 * @return string
	 */
	public function getUserID(): string
    {
		return $this->m_nUserID;
	}

	/**
	 * @return string
	 */
	public function getFormTitle(): string
    {
		return $this->m_sFormTitle;
	}

	/**
	 * @return string
	 */
	public function getUserTitle(): string
    {
		return $this->m_sUserTitle;
	}

    /**
     * @param $pwd
     * @param $iv
     * @param $ks
     */
	public function initPassword($pseudo, $pwd, $iv, $ks, $extranet) :void
    {
        $this->m_sPseudo = (string)$pseudo;
        $this->m_sPwd = (string)$pwd;
        $this->m_sIV = (string)$iv;
        $this->m_nKS = (int)$ks;
        $this->m_bExtranet = boolval((int)$extranet);
    }

    /**
     * @return int|null
     */
    public function getKS(): ?int
    {
        return $this->m_nKS;
    }

    /**
     * @return string|null
     */
    public function getPseudo(): ?string
    {
        return $this->m_sPseudo;
    }

    /**
     * @return string|null
     */
    public function getPwd(): ?string
    {
        return $this->m_sPwd;
    }

    /**
     * @return string|null
     */
    public function getIV(): ?string
    {
        return $this->m_sIV;
    }

    /**
     * @return bool
     */
    public function getExtranet() : bool
    {
        return $this->m_bExtranet;
    }

}
