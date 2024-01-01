<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use PainlessPHP\Filesystem\FilesystemObject;

class InternalRecursiveDirectoryIterator extends RecursiveDirectoryIterator
{
    public function __construct(string $path)
    {
        parent::__construct($path,
            FilesystemIterator::CURRENT_AS_PATHNAME |
            FilesystemIterator::FOLLOW_SYMLINKS
        );
    }

    /**
     * @override
     */
    public function current() : FilesystemObject
    {
        $current = parent::current();

        // Canonize dot path
        if(basename($current) === '.') {
            $current = dirname($current);
        }

        return FilesystemObject::createFromPath($current);
    }
}
