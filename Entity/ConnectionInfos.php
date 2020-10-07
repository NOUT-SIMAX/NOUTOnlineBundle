<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 15:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


class ConnectionInfos
{
	/**
	 * @var string
	 */
	protected $m_sUsername = '';

	/**
	 * @var bool
	 */
	protected $m_bExtranet = false;


    /**
     * @var string
     */
    protected $m_sExtranet = '';

    /** @var string */
    protected $m_sSessionToken = '';

	public function __construct($sUsername, $bExtranet, $sExtranet, $sSessionToken)
	{
		$this->m_sUsername      = $sUsername;
		$this->m_bExtranet      = $bExtranet;
        $this->m_sExtranet      = $sExtranet;
        $this->m_sSessionToken  = $sSessionToken;
	}

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->m_sUsername;
    }

	/**
	 * @return boolean
	 */
	public function isExtranet()
	{
		return $this->m_bExtranet;
	}

    /**
     * @return string
     */
    public function getExtranet()
    {
        return $this->m_sExtranet;
    }

    public function getConnectedUser()
    {
        return $this->m_bExtranet ? $this->m_sExtranet : $this->m_sUsername;
    }

    /**
     * @return string
     */
    public function getSessionToken()
    {
        return $this->m_sSessionToken;
    }
} 