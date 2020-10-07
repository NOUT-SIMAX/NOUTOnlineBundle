<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 10:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


use Symfony\Component\Stopwatch\Stopwatch;

abstract class NOUTCacheProvider implements NOUTCacheInterface
{
    /**
     * The namespace to prefix all cache ids with.
     *
     * @var string
     */
    protected $namespace = '';

    /**
     * @var Stopwatch
     */
    protected $__stopwatch;

    /**
     * NOUTCacheProvider constructor.
     * @param Stopwatch|null $stopwatch
     */
    public function __construct(Stopwatch $stopwatch=null)
    {
        $this->__stopwatch = $stopwatch;
    }

    /**
     * @param $function
     */
    protected function __startStopwatch($function)
    {
        if($this->__stopwatch){
            $this->__stopwatch->start(get_class($this).'::'.$function);
        }
    }

    /**
     * @param $function
     */
    protected function __stopStopwatch($function)
    {
        if($this->__stopwatch){
            $this->__stopwatch->stop(get_class($this).'::'.$function);
        }
    }

    /**
     * @param string|string[] $id
     * @param string $prefix
     * @return mixed|string
     */
    protected function _makeKey($id, string $prefix)
    {
        if (!is_array($id))
        {
            if (empty($prefix))
            {
                return $id;
            }

            if (empty($id))
            {
                return $prefix;
            }

            return sprintf('%s[%s]', $prefix, (string)$id);
        }

        if (empty($prefix))
        {
            $prefix = array_shift($id);
        }

        $key = $prefix;
        foreach($id as $subid){
            $key.=sprintf('[%s]', (string)$subid);
        }
        return $key;
    }

    /**
     * Prefixes the passed id with the configured namespace value.
     *
     * @param string|string[] $id The id to namespace.
     *
     * @return string The namespaced id.
     */
    protected function _getNamespacedId($id) : string
    {
        return $this->_makeKey($id, $this->namespace);
    }


    /**
     * Sets the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     * @param string $prefix
     *
     *
     * @return void
     */
    public function setNamespace(string $namespace, string $prefix)
    {
        $this->namespace  = $this->_makeKey($namespace, $prefix);
    }

    /**
     * Retrieves the namespace that prefixes all cache ids.
     *
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string|string[] $id The id of the cache entry to fetch.
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id)
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doFetch($this->_getNamespacedId($id));

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Returns an associative array of values for keys is found in cache.
     *
     * @param string[] $keys Array of keys to retrieve from cache
     * @return mixed[] Array of retrieved values, indexed by the specified keys.
     *                 Values that couldn't be retrieved are not contained in this array.
     */
    public function fetchMultiple(array $keys)
    {
        $this->__startStopwatch(__FUNCTION__);

        if (empty($keys)) {
            $this->__stopStopwatch(__FUNCTION__);
            return array();
        }

        // note: the array_combine() is in place to keep an association between our $keys and the $namespacedKeys
        $namespacedKeys = array_combine($keys, array_map(array($this, '_getNamespacedId'), $keys));
        $items          = $this->_doFetchMultiple($namespacedKeys);
        $foundItems     = array();

        // no internal array function supports this sort of mapping: needs to be iterative
        // this filters and combines keys in one pass
        foreach ($namespacedKeys as $requestedKey => $namespacedKey) {
            if (isset($items[$namespacedKey]) || array_key_exists($namespacedKey, $items)) {
                $foundItems[$requestedKey] = $items[$namespacedKey];
            }
        }

        $this->__stopStopwatch(__FUNCTION__);
        return $foundItems;
    }

    /**
     * Returns a boolean value indicating if the operation succeeded.
     *
     * @param array $keysAndValues  Array of keys and values to save in cache
     * @param int   $lifetime       The lifetime. If != 0, sets a specific lifetime for these
     *                              cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function saveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $this->__startStopwatch(__FUNCTION__);

        $namespacedKeysAndValues = array();
        foreach ($keysAndValues as $key => $value) {
            $namespacedKeysAndValues[$this->_getNamespacedId($key)] = $value;
        }

        $oRet = $this->_doSaveMultiple($namespacedKeysAndValues, $lifetime);

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Returns a array of boolean value indicating if the operation succeeded.
     *
     * @param string[] $keys  Array of keys
     *
     * @return bool[] of TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function deleteMultiple(array $keys)
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doDeleteMultiple($keys);

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string|string[] $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id)
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doContains($this->_getNamespacedId($id));

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Puts data into the cache.
     *
     * If a cache entry with the given id already exists, its data will be replaced.
     *
     * @param string|string[] $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0)
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doSave($this->_getNamespacedId($id), $data, $lifeTime);

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Deletes a cache entry.
     *
     * @param string|string[] $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function delete($id)
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doDelete($this->_getNamespacedId($id));

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Deletes a cache entry.
     *
     * @param string|string[] $prefix The prefix for the key.
     *
     * @return bool[] TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function deletePrefix($prefix='')
    {
        $this->__startStopwatch(__FUNCTION__);

        $keys = $this->_doListEntry($this->_getNamespacedId($prefix));
        $oRet = $this->_doDeleteMultiple($keys);

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }
    /**
     * Deletes a cache entry qui ne sont pas du prefix.
     *
     * @param string|string[] $prefix The prefix for the key.
     *
     * @return bool[] TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function deleteNotPrefix($prefix)
    {
        $this->__startStopwatch(__FUNCTION__);

        $namespace = $this->_getNamespacedId($prefix);
        $keys = $this->_doListEntry($namespace);

        //il faut faire une exclusion
        $filtered_keys = array_filter($keys, function ($key) use ($namespace){
            return strncmp($key, $namespace, strlen($namespace))!=0;
        });

        $oRet = $this->_doDeleteMultiple($filtered_keys);
        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }


    /**
     * Flushes all cache entries, globally.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll()
    {
        $this->__startStopwatch(__FUNCTION__);

        $oRet = $this->_doFlushAll();

        $this->__stopStopwatch(__FUNCTION__);
        return $oRet;
    }

    /**
     * Default implementation of doFetchMultiple. Each driver that supports multi-get should owerwrite it.
     *
     * @param string[] $keys Array of keys to retrieve from cache
     * @return array Array of values retrieved for the given keys.
     */
    protected function _doFetchMultiple(array $keys)
    {
        $returnValues = array();

        foreach ($keys as $key) {
            if (false !== ($item = $this->_doFetch($key)) || $this->_doContains($key)) {
                $returnValues[$key] = $item;
            }
        }

        return $returnValues;
    }


    /**
     * Default implementation of doDeleteMultiple. Each driver that supports multi-get should owerwrite it.
     *
     * @param string[] $keys Array of keys to retrieve from cache
     * @return array Array of values retrieved for the given keys.
     */
    protected function _doDeleteMultiple(array $keys)
    {
        $returnValues = array();

        foreach ($keys as $key) {
            $returnValues[$key] = $this->_doDelete($key);
        }

        return $returnValues;
    }

    /**
     * Default implementation of doSaveMultiple. Each driver that supports multi-put should override it.
     *
     * @param array $keysAndValues  Array of keys and values to save in cache
     * @param int   $lifetime       The lifetime. If != 0, sets a specific lifetime for these
     *                              cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    protected function _doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $success = true;

        foreach ($keysAndValues as $key => $value) {
            if (!$this->_doSave($key, $value, $lifetime)) {
                $success = false;
            }
        }

        return $success;
    }


    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     */
    abstract protected function _doFetch(string $id);


    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The beginning of the id of the cache entry to list.
     *
     * @return array|false.
     */
    abstract protected function _doListEntry(string $id);

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function _doContains(string $id);

    /**
     * Puts data into the cache.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime. If != 0, sets a specific lifetime for this
     *                           cache entry (0 => infinite lifeTime).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    abstract protected function _doSave(string $id, $data, $lifeTime = 0);

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function _doDelete(string $id);


    /**
     * Flushes all cache entries.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    abstract protected function _doFlushAll();

}