<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 27/10/2016
 * Time: 16:36
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Service;


use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheFactory;
use NOUT\Bundle\NOUTOnlineBundle\Cache\NOUTCacheProvider;
use NOUT\Bundle\NOUTOnlineBundle\Entity\Langage;

class NOUTClientCache
{
    /**
     * @var NOUTCacheProvider
     */
    private $m_clCacheSession = null;

    /**
     * @var NOUTCacheProvider
     */
    private $m_clCacheIHM = null;

    /**
     * @var NOUTCacheProvider
     */
    private $m_clCacheIcones = null;

    /**
     * @param string $sCacheDir
     * @param string $sSessionToken
     */
    public function __construct(NOUTCacheFactory $cacheFactory, $sSessionToken, Langage $clLangage)
    {
        $this->m_clCacheSession = $cacheFactory->getCache($sSessionToken, self::SOUSREPCACHE_SESSION, self::REPCACHE);
        $this->m_clCacheIHM =  $cacheFactory->getCache($clLangage->getVersionLangage(), self::SOUSREPCACHE_IHM, self::REPCACHE);
        $this->m_clCacheIcones =  $cacheFactory->getCache($clLangage->getVersionIcone(), self::SOUSREPCACHE_ICON, self::REPCACHE);
    }

    /**
     * @return NOUTCacheProvider
     */
    public function getCacheSession()
    {
        return $this->m_clCacheSession;
    }

    /**
     * @param $cache
     * @param $name
     * @return mixed
     */
    public function fetch($cache, $name)
    {
        switch($cache)
        {
        default:
            return null;
        case self::CACHE_Session:
            return $this->m_clCacheSession->fetch($name);
        case self::CACHE_IHM:
            return $this->m_clCacheIHM->fetch($name);
        case self::CACHE_Icone:
            return $this->m_clCacheIcones->fetch($name);
        }
    }

    /**
     * @param $cache
     * @param $name
     * @param $data
     * @return mixed
     */
    public function save($cache, $name, $data)
    {
        switch($cache)
        {
        default:
            return null;
        case self::CACHE_Session:
            return $this->m_clCacheSession->save($name, $data, self::TIMEOUT_1J);
        case self::CACHE_IHM:
            return $this->m_clCacheIHM->save($name, $data, self::TIMEOUT_3J);
        case self::CACHE_Icone:
            return $this->m_clCacheIcones->save($name, $data);
        }
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDColonne
     * @param $sIDEnreg
     * @param $aTabOption
     * @return false|mixed
     */
    public function fetchImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption)
    {
        $sName = $this->_sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption);
        if ($sIDFormulaire == Langage::TABL_ImageCatalogue)
        {
            return $this->m_clCacheIcones->fetch($sName);
        }
        else
        {
            return $this->m_clCacheIHM->fetch($sName);
        }
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDColonne
     * @param $sIDEnreg
     * @param $aTabOption
     * @return false|mixed
     */
    public function deleteImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption)
    {
        $sName = $this->_sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption);
        if ($sIDFormulaire == Langage::TABL_ImageCatalogue)
        {
            return $this->m_clCacheIcones->delete($sName);
        }
        else
        {
            return $this->m_clCacheIHM->delete($sName);
        }
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDColonne
     * @param $sIDEnreg
     * @param $aTabOption
     * @param $data
     * @return bool
     */
    public function saveImageFromLangage($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption, $data)
    {
        $sName = $this->_sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption);
        if ($sIDFormulaire == Langage::TABL_ImageCatalogue)
        {
            return $this->m_clCacheIcones->save($sName, $data);
        }
        else
        {
            return $this->m_clCacheIHM->save($sName, $data, self::TIMEOUT_3J);
        }
    }

    /**
     * @param $messageId
     * @param $attachmentId
     * @param $data
     * @return bool
     */
    public function saveMessagePJ($messageId, $attachmentId, $data)
    {
        return $this->m_clCacheSession->save(array('pj', $messageId, $attachmentId), $data, self::TIMEOUT_1J);
    }

    /**
     * @param $messageId
     * @param $attachmentId
     * @return false|mixed
     */
    public function fetchMessagePJ($messageId, $attachmentId)
    {
        return $this->m_clCacheSession->fetch(array('pj', $messageId, $attachmentId));
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDColonne
     * @param $sIDEnreg
     * @param $aTabOption
     * @return false|mixed
     */
    public function fetchFile($sIDContexte, $idihm, $sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption)
    {
        $sName = $this->_sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption);
        return $this->m_clCacheSession->fetch(array($sIDContexte, $idihm, 'file', $sName));
    }

    /**
     * @param $sIDFormulaire
     * @param $sIDColonne
     * @param $sIDEnreg
     * @param $aTabOption
     * @param $data
     * @return string|null
     */
    public function saveFile($sIDContexte, $idihm, $sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption, $data)
    {
        $sName = $this->_sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption);
        if ($this->m_clCacheSession->save(array($sIDContexte, $idihm, 'file', $sName), $data, self::TIMEOUT_1J))
        {
            return $sName;
        }
    }

    /**
     * @param $sIDContexte
     * @param $idihm
     * @param $name
     * @return false|mixed
     */
    public function fetchFileFromName($sIDContexte, $idihm, $name)
    {
        return $this->m_clCacheSession->fetch(array($sIDContexte, $idihm, 'file', $name));
    }

    protected function _sGetNameForFile($sIDFormulaire, $sIDColonne, $sIDEnreg, $aTabOption)
    {
        $aArray = array_filter(array($sIDFormulaire, $sIDColonne, $sIDEnreg), function($var)
        {
            return !empty($var);
        });

        $sName = implode('_', $aArray);
        $sListeOptions=$this->_sTabOptionsToString($aTabOption);
        if (!empty($sListeOptions))
        {
            $sName.='_'.$sListeOptions;
        }
        if (empty($sName))
        {
            //on a un nom vide, on génère un nom unique
            $sName = uniqid();
        }
        return $this->_sSanitizeName($sName);
    }

    protected function _sTabOptionsToString($aTabOptions)
    {
        ksort($aTabOptions);
        return implode('_', $aTabOptions);
    }

    /**
     * @param $filename
     * @return string
     */
    protected function _sSanitizeName($filename)
    {
        // a combination of various methods
        // we don't want to convert html entities, or do any url encoding
        // we want to retain the "essence" of the original file name, if possible
        // char replace table found at:
        // http://www.php.net/manual/en/function.strtr.php#98669
        $replace_chars = array(
            'Š' => 'S', 'š' => 's', 'Ð' => 'Dj', 'Ž' => 'Z', 'ž' => 'z', 'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A',
            'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I',
            'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U',
            'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss', 'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a',
            'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i',
            'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u',
            'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'þ' => 'b', 'ÿ' => 'y', 'ƒ' => 'f',
        );
        $filename = strtr($filename, $replace_chars);
        // convert & to "and", @ to "at", and # to "number"
        $filename = preg_replace(array('/[&]/', '/[@]/', '/[#]/'), array('-and-', '-at-', '-number-'), $filename);
        $filename = preg_replace('/[^(\x20-\x7F)]*/', '', $filename); // removes any special chars we missed
        $filename = str_replace(' ', '-', $filename); // convert space to hyphen
        $filename = str_replace('/', '-', $filename); // convert / to hyphen
        $filename = str_replace('\\', '-', $filename); // convert \ to hyphen
        $filename = str_replace('\'', '', $filename); // removes apostrophes
        $filename = preg_replace('/[^\w\-.]+/', '', $filename); // remove non-word chars (leaving hyphens and periods)
        $filename = preg_replace('/[\-]+/', '-', $filename); // converts groups of hyphens into one
        return strtolower($filename);
    }


    const REPCACHE              = 'NOUTClient';

    const SOUSREPCACHE_SESSION  = 'session';
    const SOUSREPCACHE_IHM      = 'ihm';
    const SOUSREPCACHE_ICON     = 'icons';

    const CACHE_Session     = 'session';
    const CACHE_IHM         = 'ihm';
    const CACHE_Icone       = 'icone';


    const TIMEOUT_1H = 3600;
    const TIMEOUT_1J = 86400;
    const TIMEOUT_2J = 172800;
    const TIMEOUT_3J = 259200;
}