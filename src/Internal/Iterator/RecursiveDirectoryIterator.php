<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use FilesystemIterator;
use RecursiveDirectoryIterator as BaseIterator;
use PainlessPHP\Filesystem\FilesystemObject;

class RecursiveDirectoryIterator extends BaseIterator
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
