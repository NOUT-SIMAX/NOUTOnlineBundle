<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 12:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

/**
 * Class ConfigurationDialogue permet de transporter les informations configurée pour le dialogue avec NOUTOnline
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 */
class ConfigurationDialogue
{
	/**
	 * @var string
	 */
	protected $m_sWSDLUri;
	/**
	 * @var bool
	 */
	protected $m_bWsdl;
	/**
	 * @var string
	 */
	protected $m_sHost;
	/**
	 * @var int
	 */
	protected $m_nPort;
	/**
	 * @var string
	 */
	protected $m_sProtocolPrefix;
	/**
	 * @var int
	 */
	protected $m_nLangCode;
	/**
	 * @var string
	 */
	protected $m_sAPIUUID;
	/**
	 * @var int
	 */
	protected $m_nDureeSession;
	/**
	 * @var string
	 */
	protected $m_sServiceAddress;

	public function __construct($sHost = '', $sPort = 0, $sProtocolPrefix = 'http://', $sAPIUUID = '')
	{
		$this->m_sServiceAddress = $sProtocolPrefix.$sHost.':'.$sPort.'/';
		$this->m_sWSDLUri       = $this->m_sServiceAddress.'getwsdl';
		$this->m_bWsdl          = true;
		$this->m_sHost          = $sHost;
		$this->m_nPort          = $sPort;
		$this->m_sProtocolPrefix = $sProtocolPrefix;

		$this->m_nLangCode    = 12;
		$this->m_sAPIUUID     = $sAPIUUID;
		$this->m_nDureeSession = 3600;
	}

	public function Init($sWSDLUri, $bWsdl = false, $sHost = false, $sPort = false, $sProtocolPrefix = 'http://')
	{
		$this->m_sWSDLUri       = $sWSDLUri;
		$this->m_bWsdl          = $bWsdl;
		$this->m_sHost          = $sHost;
		$this->m_nPort          = $sPort;
		$this->m_sProtocolPrefix = $sProtocolPrefix;
		$this->m_sServiceAddress = $sProtocolPrefix.$sHost.':'.$sPort.'/';
	}

	public function SetHost($sAddress, $sPort)
	{
		$this->m_sHost          = $sAddress;
		$this->m_nPort          = $sPort;
		$this->m_sServiceAddress = $this->m_sProtocolPrefix.$sAddress.':'.$sPort.'/';
		$this->m_sWSDLUri       = $this->m_sServiceAddress.'getwsdl';
	}

	/**
	 * @return boolean
	 */
	public function getWsdl()
	{
		return $this->m_bWsdl;
	}

	/**
	 * @param int $nDureeSession
	 */
	public function setDureeSession($nDureeSession)
	{
		$this->m_nDureeSession = $nDureeSession;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDureeSession()
	{
		return $this->m_nDureeSession;
	}

	/**
	 * @param int $nLangCode
	 */
	public function setLangCode($nLangCode)
	{
		$this->m_nLangCode = $nLangCode;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getLangCode()
	{
		return $this->m_nLangCode;
	}

	/**
	 * @return int
	 */
	public function getPort()
	{
		return $this->m_nPort;
	}

	/**
	 * @param string $sAPIUUID
	 */
	public function setAPIUUID($sAPIUUID)
	{
		$this->m_sAPIUUID = $sAPIUUID;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAPIUUID()
	{
		return $this->m_sAPIUUID;
	}
	/**
	 * @return string
	 */
	public function getHost()
	{
		return $this->m_sHost;
	}

	/**
	 * @return string
	 */
	public function getProtocolPrefix()
	{
		return $this->m_sProtocolPrefix;
	}

	/**
	 * @return string
	 */
	public function getServiceAddress()
	{
		return $this->m_sServiceAddress;
	}

	/**
	 * @return string
	 */
	public function getWSDLUri()
	{
		return $this->m_sWSDLUri;
	}
}
