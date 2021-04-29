<?php

namespace Codememory\Components\Caching\Exceptions;

use JetBrains\PhpStorm\Pure;

/**
 * Class ConfigPathNotExistException
 * @package Codememory\Components\Caching\Exceptions
 *
 * @author  Codememory
 */
class ConfigPathNotExistException extends CacheException
{

    /**
     * ConfigPathNotExistException constructor.
     *
     * @param string $path
     */
    #[Pure] public function __construct(string $path)
    {

        parent::__construct(sprintf(
            '%s config path does not exist',
            $path
        ));

    }

}