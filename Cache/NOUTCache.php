<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 18/09/14
 * Time: 16:49
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;

use Doctrine\Common\Cache\ApcCache;
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
	 * @var \Doctrine\Common\Cache\FilesystemCache|\Doctrine\Common\Cache\ApcCache|\Doctrine\Common\Cache\XcacheCache
	 */
	private $m_clPiloteCache;


	public function __construct($dir)
	{
		if (extension_loaded('apc') || extension_loaded('apcu'))
		{
			$this->m_clPiloteCache = new ApcCache();
		}
		elseif (extension_loaded('xcache'))
		{
			$this->m_clPiloteCache = new XcacheCache();
		}
		else
		{
			//$this->m_clPiloteCache = new FilesystemCache($dir, '.noutcache.data');
			$this->m_clPiloteCache = new NOUTFileCache($dir);
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
