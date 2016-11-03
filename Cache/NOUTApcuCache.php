<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 10:16
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


class NOUTApcuCache extends NOUTCacheProvider
{

    /**
     * {@inheritdoc}
     */
    protected function doFetch($id)
    {
        return apcu_fetch($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doContains($id)
    {
        return apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSave($id, $data, $lifeTime = 0)
    {
        return apcu_store($id, $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    protected function doDelete($id)
    {
        // apcu_delete returns false if the id does not exist
        return apcu_delete($id) || ! apcu_exists($id);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFlushAll()
    {
        return apcu_clear_cache();
    }


    /**
     * {@inheritdoc}
     */
    protected function doListEntry($id)
    {
        $aRet = array();
        $regex = '/^'.str_replace(array('[', ']'), array('\\[','\\]'), $id).'.*/';
        $iterator = new \APCUIterator($regex, APC_ITER_KEY);
        foreach ($iterator as $key=>$counter)
        {
            $aRet[]=$key;
        }
        return $aRet;
    }

    /**
     * {@inheritdoc}
     */
    protected function doDeleteMultiple(array $keys)
    {
        return apcu_delete($keys);
    }

    /**
     * {@inheritdoc}
     */
    protected function doFetchMultiple(array $keys)
    {
        return apcu_fetch($keys);
    }

    /**
     * {@inheritdoc}
     */
    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $result = apcu_store($keysAndValues, null, $lifetime);

        return empty($result);
    }
}