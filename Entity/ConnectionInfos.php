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
	protected $m_sUsername;

	/**
	 * @var bool
	 */
	protected $m_bExtranet;

	public function __construct($sUsername)
	{
		$this->m_sUsername = $sUsername;
		$this->m_bExtranet = false;
	}

	/**
	 * @param boolean $bExtranet
	 */
	public function setExtranet($bExtranet)
	{
		$this->m_bExtranet = $bExtranet;
		return $this;
	}

	/**
	 * @return boolean
	 */
	public function getExtranet()
	{
		return $this->m_bExtranet;
	}

	/**
	 * @param string $sUsername
	 */
	public function setUsername($sUsername)
	{
		$this->m_sUsername = $sUsername;
		return $this;
	}

	/**
	 * @return string
	 */
	public function getUsername()
	{
		return $this->m_sUsername;
	}



} 