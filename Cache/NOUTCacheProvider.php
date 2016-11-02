<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 02/11/2016
 * Time: 10:02
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


abstract class NOUTCacheProvider
{
    /**
     * The namespace to prefix all cache ids with.
     *
     * @var string
     */
    protected $namespace = '';


    protected function makeKey($id, $prefix)
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
     * @param string $id The id to namespace.
     *
     * @return string The namespaced id.
     */
    protected function getNamespacedId($id)
    {
        return $this->makeKey($id, $this->namespace);
    }


    /**
     * Sets the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     *
     * @return void
     */
    public function setNamespace($namespace, $prefix)
    {
        $this->namespace  = $this->makeKey($namespace, $prefix);
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
     * {@inheritdoc}
     */
    public function fetch($id)
    {
        return $this->doFetch($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function fetchMultiple(array $keys)
    {
        if (empty($keys)) {
            return array();
        }

        // note: the array_combine() is in place to keep an association between our $keys and the $namespacedKeys
        $namespacedKeys = array_combine($keys, array_map(array($this, 'getNamespacedId'), $keys));
        $items          = $this->doFetchMultiple($namespacedKeys);
        $foundItems     = array();

        // no internal array function supports this sort of mapping: needs to be iterative
        // this filters and combines keys in one pass
        foreach ($namespacedKeys as $requestedKey => $namespacedKey) {
            if (isset($items[$namespacedKey]) || array_key_exists($namespacedKey, $items)) {
                $foundItems[$requestedKey] = $items[$namespacedKey];
            }
        }

        return $foundItems;
    }

    /**
     * {@inheritdoc}
     */
    public function saveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $namespacedKeysAndValues = array();
        foreach ($keysAndValues as $key => $value) {
            $namespacedKeysAndValues[$this->getNamespacedId($key)] = $value;
        }

        return $this->doSaveMultiple($namespacedKeysAndValues, $lifetime);
    }

    /**
     * {@inheritdoc}
     */
    public function contains($id)
    {
        return $this->doContains($this->getNamespacedId($id));
    }

    /**
     * {@inheritdoc}
     */
    public function save($id, $data, $lifeTime = 0)
    {
        return $this->doSave($this->getNamespacedId($id), $data, $lifeTime);
    }

    /**
     * {@inheritdoc}
     */
    public function delete($id)
    {
        return $this->doDelete($this->getNamespacedId($id));
    }

    /**
     * {@inheritDoc}
     */
    public function flushAll($prefix='')
    {
        $keys = $this->doListEntry($this->getNamespacedId($prefix));
        $nb = 0;
        foreach ($keys as $key) {
            if ($this->doDelete($key))
                $nb++;
        }
        return $nb;
    }

    /**
     * Default implementation of doFetchMultiple. Each driver that supports multi-get should owerwrite it.
     *
     * @param array $keys Array of keys to retrieve from cache
     * @return array Array of values retrieved for the given keys.
     */
    protected function doFetchMultiple(array $keys)
    {
        $returnValues = array();

        foreach ($keys as $key) {
            if (false !== ($item = $this->doFetch($key)) || $this->doContains($key)) {
                $returnValues[$key] = $item;
            }
        }

        return $returnValues;
    }

    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The id of the cache entry to fetch.
     *
     * @return mixed|false The cached data or FALSE, if no cache entry exists for the given id.
     */
    abstract protected function doFetch($id);


    /**
     * Fetches an entry from the cache.
     *
     * @param string $id The beginning of the id of the cache entry to list.
     *
     * @return array|false.
     */
    abstract protected function doListEntry($id);

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    abstract protected function doContains($id);

    /**
     * Default implementation of doSaveMultiple. Each driver that supports multi-put should override it.
     *
     * @param array $keysAndValues  Array of keys and values to save in cache
     * @param int   $lifetime       The lifetime. If != 0, sets a specific lifetime for these
     *                              cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    protected function doSaveMultiple(array $keysAndValues, $lifetime = 0)
    {
        $success = true;

        foreach ($keysAndValues as $key => $value) {
            if (!$this->doSave($key, $value, $lifetime)) {
                $success = false;
            }
        }

        return $success;
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
    abstract protected function doSave($id, $data, $lifeTime = 0);

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     */
    abstract protected function doDelete($id);

}