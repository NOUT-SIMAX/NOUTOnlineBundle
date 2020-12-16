<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineState;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;

trait TokenWithNOUTOnlineVersionTrait
{
    /**
     * version du noutonline
     * @var NOUTOnlineVersion m_clVersionNO
     */
    protected $m_clVersionNO;

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return !is_null($this->m_clVersionNO);
    }

    /**
     * @return NOUTOnlineVersion|null
     */
    public function clGetNOUTOnlineVersion() : ?NOUTOnlineVersion
    {
        return $this->m_clVersionNO;
    }


    /**
     * @return string
     */
    public function getVersionNO() : string
    {
        if (is_null($this->m_clVersionNO)){
            $sVersion='';
        }
        elseif ($this->m_clVersionNO instanceof NOUTOnlineVersion) {
            $sVersion = $this->m_clVersionNO->get();
        }
        else {
            $sVersion = $this->m_clVersionNO;
        }
        return $sVersion;
    }

    /**
     * @param string|NOUTOnlineVersion $versionNO
     * @return $this
     */
    public function setVersionNO($versionNO)
    {
        if ($versionNO instanceof NOUTOnlineVersion)
        {
            $this->m_clVersionNO = $versionNO;
        }
        else
        {
            $this->m_clVersionNO = new NOUTOnlineVersion($versionNO);
        }
        return $this;
    }

    /**
     * vrai si la version courante est supérieur (ou égal suivant $bInclu)
     * @param string $sVersionMin
     * @param bool $bInclu
     * @return bool
     */
    public function isVersionSup(string $sVersionMin, bool $bInclu=true) : bool
    {
        if (is_null($this->m_clVersionNO)){
            return false;
        }

        if ($this->m_clVersionNO instanceof  NOUTOnlineVersion)
        {
            return $this->m_clVersionNO->isVersionSup($sVersionMin, $bInclu);
        }

        $clVersion = new NOUTOnlineVersion($this->m_clVersionNO);
        return $clVersion->isVersionSup($sVersionMin, $bInclu);
    }


    /**
     * @param string $sVersionMin
     * @param bool   $bInclu
     * @return NOUTOnlineState
     */
    public function clGetNOUTOnlineState(string $sVersionMin, bool $bInclu=true) : NOUTOnlineState
    {
        $ret = new  NOUTOnlineState();
        $ret->isStarted = $this->isStarted();
        if ($ret->isStarted){
            $ret->version = $this->getVersionNO();
            $ret->isRecent = $this->isVersionSup($sVersionMin, $bInclu);
        }
        return $ret;
    }
}