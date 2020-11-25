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

    /** @var  */
    protected $_versionNO;

    /**
     * @param NOUTOnlineVersion $clVersion
     * @param string            $sVersionMin
     */
    public function setVersionNO(NOUTOnlineVersion $clVersion, string $sVersionMin)
    {
        $this->isStarted = true;
        $this->_versionNO=$clVersion;
        $this->version = $clVersion->get();
        $this->isRecent = $clVersion->isVersionSup($sVersionMin);
    }
}