<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 10:25
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


class NOUTXCacheCache extends NOUTCacheProvider
{

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     */
    protected function doFetch($id)
    {
        return $this->doContains($id) ? unserialize(xcache_get($id)) : false;
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    protected function doContains($id)
    {
        return xcache_isset($id);
    }

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param string $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return xcache_set($id, serialize($data), (int) $lifeTime);
    }

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    protected function doDelete($id)
    {
        return xcache_unset($id);
    }

    /**
     * Flushes all cache entries.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    protected function doFlushAll()
    {
        $this->checkAuthorization();

        xcache_clear_cache(XC_TYPE_VAR);

        return true;
    }

    /**
     * Checks that xcache.admin.enable_auth is Off.
     *
     * @return void
     *
     * @throws \BadMethodCallException When xcache.admin.enable_auth is On.
     */
    protected function checkAuthorization()
    {
        if (ini_get('xcache.admin.enable_auth')) {
            throw new \BadMethodCallException(
                'To use all features of \Doctrine\Common\Cache\XcacheCache, '
                . 'you must set "xcache.admin.enable_auth" to "Off" in your php.ini.'
            );
        }
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
        $this->checkAuthorization();

        $varCacheCount = xcache_count(XC_TYPE_VAR);
        $aRet = array();
        for ($i = 0; $i < $varCacheCount; $i ++) {
            $data = xcache_list(XC_TYPE_VAR, $i);

            foreach($data['cache_list'] as $entry)
            {
                $key = $entry['name'];
                if (strncmp($key, $id, strlen($id))==0)
                {
                    $aRet[]=$key;
                }
            }
        }
        return $aRet;
    }
}