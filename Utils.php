<?php

namespace Codememory\Components\Caching;

use Codememory\Components\Markup\Interfaces\MarkupInterface;
use Codememory\Support\Str;

/**
 * Class Utils
 * @package Codememory\Components\Caching
 *
 * @author  Codememory
 */
class Utils
{

    public const EXPANSION = '.yaml';

    private const DEFAULT_PATH_CACHE = 'storage.cache';
    private const DEFAULT_FILE_ENCRYPTION = 'sha256';
    private const HISTORY_FILENAME = 'history';
    private const HISTORY_PATH = 'storage.cache.history';
    private const MAIN_FILE_EXTENSION = 'cache';

    /**
     * @var array
     */
    private array $config;

    /**
     * Utils constructor.
     *
     * @param MarkupInterface $markup
     * @param string          $configPath
     */
    public function __construct(MarkupInterface $markup, string $configPath)
    {

        $this->config = $markup->open($configPath)->get()['caching'] ?? [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the path where the cache should be saved
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getPath(): string
    {

        return Str::asPath($this->config['path'] ?? self::DEFAULT_PATH_CACHE);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the extension that should be substituted
     * & for the default cache file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getFileExtension(): string
    {

        return $this->config['mainFileExtension'] ?? self::MAIN_FILE_EXTENSION;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the algorithm by which the cache file name
     * & should be hashed
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function getFileEncryption(): string
    {

        return $this->config['fileEncryption'] ?? self::DEFAULT_FILE_ENCRYPTION;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Generate algorithm based hash from config
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool|string
     */
    public function createHash(string $type, string $name): bool|string
    {

        return hash($this->getFileEncryption(), $type.$name);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Generate a hash code that returns a 12-digit hash that takes
     * & 6 characters from the hash first and 6 from the end
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return bool|string
     */
    public function createHashCode(string $type, string $name): bool|string
    {

        $startHashCode = substr($this->createHash($type, $name), 0, 6);
        $endHashCode = substr($this->createHash($type, $name), -6);

        return $startHashCode . $endHashCode;

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get the path where the history file should be created
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function historyPath(): string
    {

        return Str::asPath($this->config['history']['path'] ?? self::HISTORY_PATH);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * & Get history file name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @return string
     */
    public function historyFilename(): string
    {

        return $this->config['history']['filename'] ?? self::HISTORY_FILENAME;

    }

}