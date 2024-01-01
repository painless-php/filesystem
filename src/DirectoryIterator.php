<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\Internal\Iterator\InternalFilterIterator;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use DirectoryIterator as BaseDirectoryIterator;

class DirectoryIterator extends InternalFilterIterator
{
    public function __construct(string $path, array|DirectoryIteratorConfig $config = [])
    {
        parent::__construct(
            $path,
            new BaseDirectoryIterator($path),
            $config
        );
    }

    /**
     * @override
     */
    public function current() : FilesystemObject
    {
        return FilesystemObject::createFromPath(parent::current());
    }
}
