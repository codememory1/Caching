<?php

namespace Codememory\Components\Caching\Interfaces;

use Codememory\Components\Caching\Utils;

/**
 * Interface CacheInterface
 *
 * @package Codememory\Components\Caching\Interfaces
 *
 * @author  Codememory
 */
interface CacheInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Create cache based on type and name using callback
     * you can create another cache file and write a different
     * data format to it
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string        $type
     * @param string        $name
     * @param mixed         $data
     * @param callable|null $handler
     *
     * @return CacheInterface
     */
    public function create(string $type, string $name, mixed $data, ?callable $handler = null): CacheInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get data from the cache, by default, the return will be
     * from a file that has the extension specified in the cache
     * configuration, but if you use callback, you can get a
     * different cache in a different extension
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string        $type
     * @param string        $name
     * @param callable|null $handler
     *
     * @return mixed
     */
    public function get(string $type, string $name, ?callable $handler = null): mixed;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Clear the entire folder where the cache is stored
     * and also clears the entire history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function clear(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Checking for the existence of cache and history
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function exist(string $type, string $name): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Delete cache and history if the cache does not
     * exist either in history or in a folder, then it
     * will return false
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function remove(string $type, string $name): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the cache configuration utilities
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return Utils
     */
    public function getUtils(): Utils;

}