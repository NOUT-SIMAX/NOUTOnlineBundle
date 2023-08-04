<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/01/2017
 * Time: 11:28
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Entity;


class NOUTOnlineVersion
{
    /** @var string */
    protected $m_sVersion='';

    /**
     * NOUTOnlineVersion constructor.
     *
     * @param string $sVersion
     */
    public function __construct(string $sVersion)
    {
        $this->m_sVersion=$sVersion;
    }

    /**
     * @return string
     */
    public function get(): string
    {
        return $this->m_sVersion;
    }

    /**
     * @param string $sVersionMin
     * @param bool $bInclu
     * @return bool
     */
    public function isVersionSup(string $sVersionMin, bool $bInclu = true) : bool
    {
        if (empty($this->m_sVersion)){
            return false;
        }

        if ($sVersionMin instanceof NOUTOnlineVersion){
            $sVersionMin = $sVersionMin->get();
        }

        $aVersionCur = array_slice(explode('.', $this->m_sVersion), -2);
        $aVersionMin = array_slice(explode('.', $sVersionMin), -2);

        if ((int)$aVersionCur[0]>(int)$aVersionMin[0]){
            return true;
        }
        if ((int)$aVersionCur[0]<(int)$aVersionMin[0]){
            return false;
        }

        if ((int)$aVersionCur[1]>(int)$aVersionMin[1]){
            return true;
        }
        if ((int)$aVersionCur[1]<(int)$aVersionMin[1]){
            return false;
        }
        return $bInclu;
    }

    const SUPPORT_SSO_OPENID = '2048.01';
    const SUPPORT_RESTRICTION_WHITESPACE = '2317.01';
    const SUPPORT_BREAKROW = '2244.01';
    const SUPPORT_CREATE_FIELD = '2332.01';
}
