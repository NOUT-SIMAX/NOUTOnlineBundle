<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


class NOUTOnlineState
{
    /** @var string  */
    public $version='N/A';

    /** @var bool  */
    public $isStarted = false;

    /** @var bool  */
    public $isRecent=false;

    /** @var bool  */
    public $isSIMAXStarter=false;

    /** @var  */
    protected $_versionNO;

    /**
     * @param NOUTOnlineVersion $clVersion
     * @param string            $sVersionMin
     */
    public function setVersionNO(NOUTOnlineVersion $clVersion, string $sVersionMin, bool $bIsSIMAXStarter)
    {
        $this->isStarted = true;
        $this->_versionNO=$clVersion;
        $this->version = $clVersion->get();
        $this->isRecent = $clVersion->isVersionSup($sVersionMin);
        $this->isSIMAXStarter = $bIsSIMAXStarter;
    }
}