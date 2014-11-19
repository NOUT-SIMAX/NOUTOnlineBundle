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

	public $m_sWSDLUri;
	public $m_bWsdl;
	public $m_sHost;
	public $m_nPort;
	public $m_sProtocolPrefix;
	public $m_nLangCode;
	public $m_sAPIUUID;
	public $m_nDureeSession;
	public $m_sServiceAddress;

	public function __construct($sHost = false, $sPort = false, $sProtocolPrefix = 'http://')
	{
		$this->m_sServiceAddress=$sProtocolPrefix.$sHost.':'.$sPort.'/';
		$this->m_sWSDLUri=$this->m_sServiceAddress.'getwsdl';
		$this->m_bWsdl=true;
		$this->m_sHost=$sHost;
		$this->m_nPort=$sPort;
		$this->m_sProtocolPrefix=$sProtocolPrefix;

		$this->m_nLangCode=12;
		$this->m_sAPIUUID='';
		$this->m_nDureeSession=3600;
	}

	public function SetHost($sAddress, $sPort)
	{
		$this->m_sHost=$sAddress;
		$this->m_nPort=$sPort;
		$this->m_sServiceAddress=$this->m_sProtocolPrefix.$sAddress.':'.$sPort.'/';
		$this->m_sWSDLUri=$this->m_sServiceAddress.'getwsdl';
	}

	public function Init($sWSDLUri,$bWsdl = false,$sHost = false,$sPort = false, $sProtocolPrefix = 'http://')
	{
		$this->m_sWSDLUri=$sWSDLUri;
		$this->m_bWsdl=$bWsdl;
		$this->m_sHost=$sHost;
		$this->m_nPort=$sPort;
		$this->m_sProtocolPrefix=$sProtocolPrefix;
		$this->m_sServiceAddress=$sProtocolPrefix.$sHost.':'.$sPort.'/';
	}
}