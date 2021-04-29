<?php

namespace Codememory\Components\Caching\Interfaces;

/**
 * Interface CacheHistoryInterface
 * @package Codememory\Components\Caching\Interfaces
 *
 * @author  Codememory
 */
interface CacheHistoryInterface
{

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Adding a new history to the history file, using callback
     * you can add additional history information
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string        $type
     * @param string        $name
     * @param callable|null $handler
     *
     * @return CacheHistoryInterface
     */
    public function add(string $type, string $name, ?callable $handler = null): CacheHistoryInterface;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Check if history has a part of some history by type and name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function has(string $type, string $name): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Delete history from history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function remove(string $type, string $name): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Clear entire history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return bool
     */
    public function clear(): bool;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Get history by type and name from the entire history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return array
     */
    public function get(string $type, string $name): array;

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the entire history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return array
     */
    public function all(): array;

}