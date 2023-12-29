<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use RecursiveFilterIterator as GlobalRecursiveFilterIterator;
use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\Internal\Trait\HasConfig;
use PainlessPHP\Filesystem\DirectoryContentIteratorConfiguration;

class RecursiveFilterIterator extends GlobalRecursiveFilterIterator
{
    use HasConfig;

    public function __construct(RecursiveDirectoryIterator $iterator, private string $path, array|DirectoryContentIteratorConfiguration $config)
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
    public function getChildren(): ?RecursiveFilterIterator
    {
        /** @var RecursiveDirectoryIterator $inner */
        $inner = $this->getInnerIterator();
        return new self($inner->getChildren(), $this->path, $this->config);
    }
}
