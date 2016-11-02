<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 19/09/14
 * Time: 11:18
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;

class NOUTFileCache extends NOUTCacheProvider
{
    const FILE_EXTENSION = '.noutcache.data';

    protected function makeKey($id, $prefix)
    {
        if (!is_array($id)){
            return sprintf('%s/%s', $prefix, (string)$id);
        }

        $key = $prefix;
        foreach($id as $subid){
            $key.=sprintf('/%s', (string)$subid);
        }
        return $key;
    }

    protected function getNamespacedId($id)
    {
        $key = $this->makeKey($id, $this->namespace);
        $key.=self::FILE_EXTENSION;
        return $key;
    }

	/**
	 * {@inheritdoc}
	 */
	protected function doFetch($id)
	{
		if (!file_exists($id))
		{
			return false;
		}

		return unserialize(file_get_contents($id));
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doContains($id)
	{
		return file_exists($id);
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doSave($id, $data, $lifeTime = 0)
	{
        $dir = dirname($id);
		if (!file_exists($dir))
		{
			if (!@mkdir($dir, 0777, true))
			{
				return false;
			}
		}
		file_put_contents($id, serialize($data));
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doDelete($id)
	{
        if (file_exists($id))
        {
            unlink($id);
        }

		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doFlush()
	{
		array_map('unlink', glob($this->namespace.'/*'));
		return true;
	}

	/**
	 * {@inheritdoc}
	 */
	protected function doGetStats()
	{
		return;
	}


    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The beginning of the id of the cache entry to list.
     *
     * @return array|false.
     */
    protected function doListEntry($id)
    {
        //$entry_list = apcu_cache_info()['cache_list'];

        $aRet = array();

        return $aRet;
    }
}
