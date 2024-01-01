<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use RecursiveFilterIterator as GlobalRecursiveFilterIterator;
use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\Internal\Trait\HasConfig;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Internal\Trait\FiltersFilesystemObjects;

class InternalRecursiveFilterIterator extends GlobalRecursiveFilterIterator
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

        if($this->shouldFilterScan($file)) {
            return false;
        }

        return true;
    }

    private function shouldFilterScan(FilesystemObject $filesystemObject) : bool
    {
        return $this->shouldFilter($filesystemObject, $this->config->scanFilters);
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
