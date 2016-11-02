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
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return $this->doContains($id) ? unserialize(xcache_get($id)) : false;
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return xcache_isset($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return xcache_set($id, serialize($data), (int) $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        return xcache_unset($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlush()
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
     * {@inheritdoc}
     */
    protected function doGetStats()
    {
        $this->checkAuthorization();

        $info = xcache_info(XC_TYPE_VAR, 0);
        return array(
            Cache::STATS_HITS   => $info['hits'],
            Cache::STATS_MISSES => $info['misses'],
            Cache::STATS_UPTIME => null,
            Cache::STATS_MEMORY_USAGE      => $info['size'],
            Cache::STATS_MEMORY_AVAILABLE  => $info['avail'],
        );
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
        $entry_list = apcu_cache_info()['cache_list'];

        $aRet = array();

        return $aRet;
    }
}