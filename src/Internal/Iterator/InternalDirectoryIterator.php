<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use FilesystemIterator;
use PainlessPHP\Filesystem\FilesystemObject;

class InternalDirectoryIterator extends FilesystemIterator
{
    public function __construct(string $path)
    {
        parent::__construct($path, FilesystemIterator::CURRENT_AS_PATHNAME | FilesystemIterator::SKIP_DOTS);
    }

    /**
     * @override
     */
    public function current() : FilesystemObject
    {
        return FilesystemObject::createFromPath(parent::current());
    }
}
