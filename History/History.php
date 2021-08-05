<?php

namespace Codememory\Components\Caching\History;

use Codememory\Components\Caching\Cache;
use Codememory\Components\Caching\Interfaces\CacheHistoryInterface;
use Codememory\Components\Caching\Utils;
use Codememory\Components\Markup\Markup;
use Codememory\FileSystem\Interfaces\FileInterface;
use Codememory\Support\Arr;
use Codememory\Support\Str;

/**
 * Class History
 * @package Codememory\Components\Caching\History
 *
 * @author  Codememory
 */
class History implements CacheHistoryInterface
{

    /**
     * @var FileInterface
     */
    private FileInterface $filesystem;

    /**
     * @var Cache
     */
    private Cache $cache;

    /**
     * @var array
     */
    private array $histories;

    /**
     * @var string|null
     */
    private ?string $historyPath = null;

    /**
     * @param FileInterface $filesystem
     * @param Cache         $cache
     */
    public function __construct(FileInterface $filesystem, Cache $cache)
    {

        $this->filesystem = $filesystem;
        $this->cache = $cache;
        $this->histories = $this->all();

    }

    /**
     * @inheritDoc
     */
    public function add(string $type, string $name, ?callable $handler = null): CacheHistoryInterface
    {

        $info = [
            'filename'      => $this->fileHash($type, $name),
            'mainExtension' => $this->cache->utils->getFileExtension()
        ];

        $merge = null === $handler ? $info : array_merge($info, call_user_func($handler));

        $this->histories[$this->hashCode($type, $name)] = $merge;

        $this->updateHistory(function (array &$data) {
            $data = $this->histories;
        });

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function has(string $type, string $name): bool
    {

        return Arr::exists($this->histories, $this->hashCode($type, $name));

    }

    /**
     * @inheritDoc
     */
    public function remove(string $type, string $name): bool
    {

        if ($this->has($type, $name)) {
            unset($this->histories[$this->hashCode($type, $name)]);

            return $this->updateHistory(function (array &$data) {
                $data = $this->histories;
            });
        }

        return false;

    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {

        $this->histories = [];

        return $this->updateHistory(function (array &$data) {
            $data = [];
        });

    }

    /**
     * @inheritDoc
     */
    public function get(string $type, string $name): array
    {

        if ($this->has($type, $name)) {
            return $this->histories[$this->hashCode($type, $name)];
        }

        return [];

    }

    /**
     * @inheritDoc
     */
    public function all(): array
    {

        $path = $this->cache->utils->historyPath();
        $filename = $this->cache->utils->historyFilename();
        $fullPath = sprintf('%s/%s', $path, $filename);

        $this->historyPath = $fullPath;

        if ($this->cache->fs->exist($fullPath . Utils::EXPANSION)) {
            return $this->cache->markup->open($fullPath)->get() ?? [];
        }

        return [];

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the hash code of the file consisting
     * of the type and name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return string
     */
    public function hashCode(string $type, string $name): string
    {

        return $this->cache->utils->createHashCode($type, $name);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * Returns the full file name hash consisting
     * of type and name
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param string $type
     * @param string $name
     *
     * @return string
     */
    public function fileHash(string $type, string $name): string
    {

        return $this->cache->utils->createHash($type, $name);

    }

    /**
     * =>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>=>
     * The main method for updating the history file
     * <=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=<=
     *
     * @param callable $handler
     *
     * @return bool
     */
    private function updateHistory(callable $handler): bool
    {

        $dir = $this->cache->utils->historyPath();

        if(!$this->filesystem->exist($dir)) {
            $this->filesystem->mkdir($dir, 0777, true);
        }

        $this->cache->markup
            ->setFlags(Markup::CREATE_NON_EXIST)
            ->open($this->historyPath)
            ->change($handler);

        return true;

    }

}