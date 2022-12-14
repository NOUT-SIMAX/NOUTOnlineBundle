<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineState;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;

trait TraitTokenWithNOUTOnlineVersion
{
    /**
     * version du noutonline
     * @var NOUTOnlineVersion|string m_clVersionNO
     */
    protected $m_clVersionNO = null;

    /** @var bool  */
    protected $m_bIsSIMAXStarter = false;

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        if (empty($this->m_clVersionNO)){
            return false;
        }

        if ((!$this->m_clVersionNO instanceof NOUTOnlineVersion) && (strcmp($this->m_clVersionNO, "N/A")==0)){
            return false;
        }

        return true;
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
        if (is_null($versionNO) || $versionNO instanceof NOUTOnlineVersion)
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
     * @return bool
     */
    public function isSIMAXStarter(): bool
    {
        return $this->m_bIsSIMAXStarter;
    }

    /**
     * @param bool $bIsSIMAXStarter
     * @return TraitTokenWithNOUTOnlineVersion
     */
    public function setIsSIMAXStarter(bool $bIsSIMAXStarter)
    {
        $this->m_bIsSIMAXStarter = $bIsSIMAXStarter;
        return $this;
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