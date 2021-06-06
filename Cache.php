<?php

namespace Codememory\Components\Caching;

use Codememory\Components\Caching\Exceptions\ConfigPathNotExistException;
use Codememory\Components\Caching\History\History;
use Codememory\Components\Caching\Interfaces\CacheInterface;
use Codememory\Components\GlobalConfig\GlobalConfig;
use Codememory\Components\Markup\Interfaces\MarkupTypeInterface;
use Codememory\Components\Markup\Markup;
use Codememory\FileSystem\Interfaces\FileInterface;
use LogicException;

/**
 * Class Cache
 * @package Codememory\Components\Caching
 *
 * @author  Codememory
 */
class Cache implements CacheInterface
{

    /**
     * @var Markup
     */
    public Markup $markup;

    /**
     * @var FileInterface
     */
    public FileInterface $fs;

    /**
     * @var Utils
     */
    public Utils $utils;

    /**
     * @var string|null
     */
    private ?string $configPathWithExpansion = null;

    /**
     * @var string|null
     */
    private ?string $configPathWithoutExpansion = null;

    /**
     * @var History|null
     */
    private ?History $history = null;

    /**
     * Cache constructor.
     *
     * @param MarkupTypeInterface $markupType
     * @param FileInterface       $fileSystem
     *
     * @throws ConfigPathNotExistException
     */
    public function __construct(MarkupTypeInterface $markupType, FileInterface $fileSystem)
    {

        $this->markup = new Markup($markupType);
        $this->fs = $fileSystem;

        $this->setConfigPath(GlobalConfig::get('caching.fileConfig'));

        $this->utils = $this->setUtils();

    }

    /**
     * @return History
     */
    public function history(): History
    {

        if (!$this->history instanceof History) {
            $this->history = new History($this);
        }

        return $this->history;

    }

    /**
     * @inheritDoc
     */
    public function create(string $type, string $name, mixed $data, ?callable $handler = null): CacheInterface
    {

        $fullPath = $this->getFullPath($type, $name);
        $fullPathWithoutFile = $this->getFullPath($type, $name, true);

        if (!$this->fs->exist($fullPathWithoutFile)) {
            $this->fs->mkdir($fullPathWithoutFile, 0777, true);
        }

        $this->fs->writer
            ->open($this->getPathWithExtension($fullPath), 'w+', true)
            ->put(serialize($data));

        $storyAddition = [];

        if (null !== $handler) {
            call_user_func_array($handler, [$this->fs, $fullPath, $data, &$storyAddition]);
        }

        $this->history()->add($type, $name, fn () => $storyAddition);

        return $this;

    }

    /**
     * @inheritDoc
     */
    public function get(string $type, string $name, ?callable $handler = null): mixed
    {

        if ($this->exist($type, $name)) {
            if (null === $handler) {
                $read = $this->fs->reader
                    ->open($this->getPathWithExtension($this->getFullPath($type, $name)))
                    ->read();
                return unserialize($read);
            }

            return call_user_func_array($handler, [$this->fs, $this->getFullPath($type, $name)]);
        }

        return [];

    }

    /**
     * @inheritDoc
     */
    public function clear(): bool
    {

        if ($this->fs->exist($this->utils->getPath())) {
            $this->fs->remove($this->utils->getPath(), true, false);
            $this->history()->clear();

            return true;
        }

        return false;

    }

    /**
     * @param string $type
     * @param string $name
     *
     * @return bool
     */
    public function exist(string $type, string $name): bool
    {

        $existFile = $this->fs->exist($this->getPathWithExtension($this->getFullPath($type, $name)));

        return $this->history()->has($type, $name) && $existFile;

    }

    /**
     * @inheritDoc
     */
    public function remove(string $type, string $name): bool
    {
        if ($this->exist($type, $name)) {
            $this->fs->remove($this->getFullPath($type, $name, true), true, true);
            $this->history()->remove($type, $name);

            return true;
        }

        return false;

    }

    /**
     * @param string $path
     *
     * @return void
     * @throws ConfigPathNotExistException
     */
    private function setConfigPath(string $path): void
    {

        $this->configPathWithExpansion = $path . Utils::EXPANSION;
        $this->configPathWithoutExpansion = $path;

        if (!$this->fs->exist($this->configPathWithExpansion)) {
            throw new ConfigPathNotExistException($path);
        }

    }

    /**
     * @param string $type
     * @param string $name
     * @param bool   $withoutFile
     *
     * @return string
     */
    private function getFullPath(string $type, string $name, bool $withoutFile = false): string
    {

        $path = $this->utils->getPath();
        $hashCode = $this->utils->createHashCode($type, $name);
        $hash = $this->utils->createHash($type, $name);
        $extension = $this->utils->getFileExtension();

        $fullPath = sprintf('%s/%s/%s.%s', $path, $hashCode, $hash, $extension);

        if ($withoutFile) {
            $fullPathWithoutFile = explode('/', $fullPath);

            unset($fullPathWithoutFile[array_key_last($fullPathWithoutFile)]);

            return implode('/', $fullPathWithoutFile);

        }

        return mb_substr($fullPath, 0, mb_stripos($fullPath, '.'));

    }

    /**
     * @param string      $path
     * @param bool        $mainExtension
     * @param string|null $extension
     *
     * @return string
     */
    private function getPathWithExtension(string $path, bool $mainExtension = true, ?string $extension = null): string
    {

        if ($mainExtension) {
            $extension = $this->utils->getFileExtension();
        }

        return $path . '.' . $extension;

    }

    /**
     * @return Utils
     */
    private function setUtils(): Utils
    {

        if (null === $this->configPathWithExpansion) {
            throw new LogicException('Cache configuration path not specified');
        }

        return new Utils($this->markup, $this->configPathWithoutExpansion);

    }

}