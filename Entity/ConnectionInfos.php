<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/11/14
 * Time: 15:22
 */

namespace NOUT\Bundle\ContextsBundle\Entity;


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

	public function __construct($sUsername, $bExtranet, $sExtranet)
	{
		$this->m_sUsername = $sUsername;
		$this->m_bExtranet = $bExtranet;
        $this->m_sExtranet = $sExtranet;
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

} 