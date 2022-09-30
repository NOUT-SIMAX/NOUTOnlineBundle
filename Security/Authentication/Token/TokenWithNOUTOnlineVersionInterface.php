<?php


namespace NOUT\Bundle\NOUTOnlineBundle\Security\Authentication\Token;


use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineState;
use NOUT\Bundle\NOUTOnlineBundle\Entity\NOUTOnlineVersion;

interface TokenWithNOUTOnlineVersionInterface
{
    /**
     * @param $versionNO
     * @return mixed
     */
    public function setVersionNO($versionNO);

    /**
     * @return string
     */
    public function getVersionNO() : string;

    /**
     * @return NOUTOnlineVersion
     */
    public function clGetNOUTOnlineVersion() : ?NOUTOnlineVersion;

    /**
     * @param string $sVersionMin
     * @param bool   $bInclu
     * @return bool
     */
    public function isVersionSup(string $sVersionMin, bool $bInclu=true) : bool;

    /**
     * @return bool
     */
    public function isStarted(): bool;

    /**
     * @param string $sVersionMin
     * @param bool   $bInclu
     * @return NOUTOnlineState
     */
    public function clGetNOUTOnlineState(string $sVersionMin, bool $bInclu=true) : NOUTOnlineState;

    /**
     * @return bool
     */
    public function isSIMAXStarter(): bool;

    /**
     * @param bool $bIsSIMAXStarter
     * @return mixed
     */
    public function setIsSIMAXStarter(bool $bIsSIMAXStarter);
}