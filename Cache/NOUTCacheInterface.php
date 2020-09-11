<?php
/**
 * Created by PhpStorm.
 * User: Ninon
 * Date: 03/11/2016
 * Time: 15:03
 */

namespace NOUT\Bundle\NOUTOnlineBundle\Cache;


interface NOUTCacheInterface 
{
    /**
     * Sets the namespace to prefix all cache ids with.
     *
     * @param string $namespace
     * @param string $prefix
     *
     * @return void
     */
    public function setNamespace(string $namespace, string $prefix);

    /**
     * Retrieves the namespace that prefixes all cache ids.
     *
     * @return string
     */
    public function getNamespace();


    /**
     * Fetches an entry from the cache.
     *
     * @param string|string[] $id The id of the cache entry to fetch.
     *
     * @return mixed The cached data or FALSE, if no cache entry exists for the given id.
     */
    public function fetch($id);

    /**
     * Returns an associative array of values for keys is found in cache.
     *
     * @param string[] $keys Array of keys to retrieve from cache
     * @return mixed[] Array of retrieved values, indexed by the specified keys.
     *                 Values that couldn't be retrieved are not contained in this array.
     */
    public function fetchMultiple(array $keys);

    /**
     * Returns a boolean value indicating if the operation succeeded.
     *
     * @param array $keysAndValues  Array of keys and values to save in cache
     * @param int   $lifetime       The lifetime. If != 0, sets a specific lifetime for these
     *                              cache entries (0 => infinite lifeTime).
     *
     * @return bool TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function saveMultiple(array $keysAndValues, $lifetime = 0);

    /**
     * Returns a array of boolean value indicating if the operation succeeded.
     *
     * @param string[] $keys  Array of keys
     *
     * @return bool[] of TRUE if the operation was successful, FALSE if it wasn't.
     */
    public function deleteMultiple(array $keys);

    /**
     * Tests if an entry exists in the cache.
     *
     * @param string|string[] $id The cache id of the entry to check for.
     *
     * @return bool TRUE if a cache entry exists for the given cache id, FALSE otherwise.
     */
    public function contains($id);

    /**
     * Puts data into the cache.
     *
     * If a cache entry with the given id already exists, its data will be replaced.
     *
     * @param string $id       The cache id.
     * @param mixed  $data     The cache entry/data.
     * @param int    $lifeTime The lifetime in number of seconds for this cache entry.
     *                         If zero (the default), the entry never expires (although it may be deleted from the cache
     *                         to make place for other entries).
     *
     * @return bool TRUE if the entry was successfully stored in the cache, FALSE otherwise.
     */
    public function save($id, $data, $lifeTime = 0);

    /**
     * Deletes a cache entry.
     *
     * @param string $id The cache id.
     *
     * @return bool TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function delete($id);

    /**
     * Deletes a cache entry.
     *
     * @param string|string[] $prefix The prefix for the key.
     *
     * @return bool[] TRUE if the cache entry was successfully deleted, FALSE otherwise.
     *              Deleting a non-existing entry is considered successful.
     */
    public function deletePrefix($prefix='');

    /**
     * Flushes all cache entries, globally.
     *
     * @return bool TRUE if the cache entries were successfully flushed, FALSE otherwise.
     */
    public function flushAll();
}