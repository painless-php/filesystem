<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use RecursiveFilterIterator;
use PainlessPHP\Filesystem\Internal\Trait\HasConfig;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Internal\Trait\FiltersFilesystemObjects;

class InternalRecursiveFilterIterator extends RecursiveFilterIterator
{
    use HasConfig, FiltersFilesystemObjects;

    public function __construct(InternalRecursiveDirectoryIterator $iterator, private string $path, array|DirectoryIteratorConfig $config)
    {
        parent::__construct($iterator);
        $this->setConfig($config);
    }


    public function accept() : bool
    {
        $file = $this->getInnerIterator()->current();

        if($file->getPathname() === $this->path) {
            return false;
        }

        if($file->getFilename() === '..') {
            return false;
        }

        if($this->shouldFilter($file, $this->config->recursionFilters)) {
            return false;
        }

        return true;
    }

    /**
     * @override
     *
     * Override to make sure that recursing iterators are instantiated correctly
     *
     */
    public function getChildren(): ?InternalRecursiveFilterIterator
    {
        /** @var InternalRecursiveDirectoryIterator $inner */
        $inner = $this->getInnerIterator();
        return new self($inner->getChildren(), $this->path, $this->config);
    }
}
