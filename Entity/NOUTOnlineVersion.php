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
    protected $m_sVersion;

    /**
     * NOUTOnlineVersion constructor.
     *
     * @param string $sVersion
     */
    public function __construct($sVersion)
    {
        $this->m_sVersion=$sVersion;
    }

    /**
     * @return string
     */
    public function get()
    {
        return $this->m_sVersion;
    }

    /**
     * @param      $sVersionMin
     * @param bool $bInclu
     * @return bool
     */
    public function isVersionSup($sVersionMin, $bInclu = true)
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

}