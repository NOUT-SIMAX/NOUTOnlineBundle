<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 23/07/14
 * Time: 12:22
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;

use NOUT\Bundle\NOUTOnlineBundle\Service\DynamicConfigurationLoader;

/**
 * Class ConfigurationDialogue permet de transporter les informations configurée pour le dialogue avec NOUTOnline
 * @package NOUT\Bundle\NOUTOnlineBundle\Entity
 */
class ConfigurationDialogue
{
	/**
	 * @var string
	 */
	protected $m_sWSDLUri='';
	/**
	 * @var bool
	 */
	protected $m_bWsdl=true;
	/**
	 * @var string
	 */
	protected $m_sHost='';
	/**
	 * @var int
	 */
	protected $m_nPort=0;
	/**
	 * @var string
	 */
	protected $m_sProtocolPrefix='';
	/**
	 * @var int
	 */
	protected $m_nLangCode=0;
	/**
	 * @var string
	 */
	protected $m_sAPIUUID='';
	/**
	 * @var int
	 */
	protected $m_nDureeSession=3600;

    /**
     * @var int
     */
    protected $m_nVersionDialoguePref=1;
	/**
	 * @var string
	 */
	protected $m_sServiceAddress='';

	/**
	 * @var string
	 */
	protected $m_sVersion='';
	/**
	 * @var string
	 */
	protected $m_sSociete='';

    /**
     * @var string
     */
    protected $m_sModeAuth='';

    /**
     * @var string
     */
    protected $m_sSecret='';

	public function __construct(
        DynamicConfigurationLoader $loader,
        string $sVersion='',
        string $sSociete='',
        int $nVersionDialPref=1
    )
	{
        $this->m_nVersionDialoguePref=$nVersionDialPref;
        $this->m_sVersion=$sVersion;
        $this->m_sSociete=$sSociete;

        $sAPIUUID = $loader->getParameter('apiuuid');
        $sHost = $loader->getParameter('address');
        $sPort = $loader->getParameter('port');
        $sProtocolPrefix = $loader->getParameter('protocole');
        $aAuth = $loader->getParameter('auth');
        
        $this->m_sAPIUUID     = trim($sAPIUUID);
		$this->m_sServiceAddress = $sProtocolPrefix.$sHost.':'.$sPort.'/';
		$this->m_sWSDLUri       = $this->m_sServiceAddress.'getwsdl';
        if (!empty($this->m_sAPIUUID)){
            $this->m_sWSDLUri.='!&APIUUID='.urlencode($this->m_sAPIUUID);
        }
		$this->m_sHost          = $sHost;
		$this->m_nPort          = $sPort;
		$this->m_sProtocolPrefix = $sProtocolPrefix;

        $this->m_sModeAuth = isset($aAuth['mode']) ? $aAuth['mode'] : '';
        $this->m_sSecret = isset($aAuth['secret']) ? trim($aAuth['secret']) : '';
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
	public function getWsdl(): bool
    {
		return $this->m_bWsdl;
	}

	/**
	 * @param int $nDureeSession
     * @return $this
	 */
	public function setDureeSession(int $nDureeSession): ConfigurationDialogue
    {
		$this->m_nDureeSession = $nDureeSession;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getDureeSession(): int
    {
		return $this->m_nDureeSession;
	}

	/**
	 * @param int $nLangCode
     * @return $this
	 */
	public function setLangCode(int $nLangCode): ConfigurationDialogue
    {
		$this->m_nLangCode = $nLangCode;

		return $this;
	}

	/**
	 * @return int
	 */
	public function getLangCode(): int
    {
		return $this->m_nLangCode;
	}

	/**
	 * @return int
	 */
	public function getPort(): int
    {
		return $this->m_nPort;
	}

	/**
	 * @param string $sAPIUUID
     * @return $this
	 */
	public function setAPIUUID(string $sAPIUUID): ConfigurationDialogue
    {
		$this->m_sAPIUUID = $sAPIUUID;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getAPIUUID(): string
    {
		return $this->m_sAPIUUID;
	}
	/**
	 * @return string
	 */
	public function getHost(): string
    {
		return $this->m_sHost;
	}

	/**
	 * @return string
	 */
	public function getProtocolPrefix(): string
    {
		return $this->m_sProtocolPrefix;
	}

	/**
	 * @return string
	 */
	public function getServiceAddress(): string
    {
		return $this->m_sServiceAddress;
	}

	/**
	 * @return string
	 */
	public function getWSDLUri(): string
    {
		return $this->m_sWSDLUri;
	}

	/**
	 * @return string
	 */
	public function getVersion(): string
    {
		return $this->m_sVersion;
	}

	/**
	 * @return string
	 */
	public function getSociete(): string
    {
		return $this->m_sSociete;
	}

    /**
     * @return int
     */
	public function getVersionDialoguePref(): int
    {
        return $this->m_nVersionDialoguePref;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->m_sSecret;
    }

    /**
     * @return string
     */
    public function getModeAuth(): string
    {
        return $this->m_sModeAuth;
    }

	const HTTP_SIMAX_CLIENT           = 'x-SIMAXService-Client';
	const HTTP_SIMAX_CLIENT_IP        = 'x-SIMAXService-Client-IP';
	const HTTP_SIMAX_CLIENT_Version   = 'x-SIMAXService-Client-Version';
}
