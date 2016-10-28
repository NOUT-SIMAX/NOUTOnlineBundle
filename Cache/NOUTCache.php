<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/09/14
 * Time: 16:49
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;

use Doctrine\Common\Cache\ApcuCache;
use Doctrine\Common\Cache\CacheProvider;
use Doctrine\Common\Cache\FilesystemCache;
use Doctrine\Common\Cache\XcacheCache;


/**
 * Class NOUTCache, met en cache en fonction des extensions disponible
 * @package NOUT\Bundle\NOUTOnlineBundle\Cache
 */
class NOUTCache extends CacheProvider
{
	/**
	 * @var \Doctrine\Common\Cache\FilesystemCache|NOUTFileCache|\Doctrine\Common\Cache\ApcuCache|\Doctrine\Common\Cache\XcacheCache
	 */
	private $m_clPiloteCache;


	public function __construct($dir, $prefix1, $prefix2, $prefix3='')
	{
		if (extension_loaded('apc') || extension_loaded('apcu'))
		{
			$this->m_clPiloteCache = new ApcuCache();

            $prefix = $this->_sMakePrefix(array($prefix1, $prefix2, $prefix3), '_');
			if (!empty($prefix))
            {
                $this->setNamespace($prefix);
            }
		}
		elseif (extension_loaded('xcache'))
		{
			$this->m_clPiloteCache = new XcacheCache();
            $prefix = $this->_sMakePrefix(array($prefix1, $prefix2, $prefix3), '_');
			if (!empty($prefix))
            {
                $this->setNamespace($prefix);
            }
		}
		else
		{
			$this->m_clPiloteCache = new NOUTFileCache($this->_sMakePrefix(array($dir, $prefix1, $prefix2, $prefix3), '/'));
		}
	}

    protected function _sMakePrefix($aArray, $sep)
    {
        $aArray = array_filter($aArray, function($var)
        {
            // retourne lorsque l'entrée est impaire
            return !empty($var);
        });

        return implode($sep, $aArray);
    }

    public function destroy()
    {
        if ($this->m_clPiloteCache instanceof NOUTFileCache)
        {
            $this->m_clPiloteCache->destroy();
        }
        else
        {
            $this->doFlush();
        }
    }

	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id)
	{
		return $this->m_clPiloteCache->doFetch($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id)
	{
		return $this->m_clPiloteCache->doContains($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0)
	{
		return $this->m_clPiloteCache->doSave($id, $data, $lifeTime);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doDelete($id)
	{
		return $this->m_clPiloteCache->doDelete($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFlush()
	{
		return $this->m_clPiloteCache->doFlush();
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doGetStats()
	{
		return $this->m_clPiloteCache->doGetStats();
	}
}
