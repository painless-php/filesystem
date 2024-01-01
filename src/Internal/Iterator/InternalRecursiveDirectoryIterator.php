<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use FilesystemIterator;
use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
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

        try {
            return FilesystemObject::createFromPath($current);
        }
        catch(FileNotFoundException $e) {
            if(basename($current) === '..') {
                return new Directory($current);
            }

            throw $e;
        }
    }
}
