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