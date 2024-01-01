<?php

namespace PainlessPHP\Filesystem\Internal\Iterator;

use Iterator;
use FilterIterator;
use PainlessPHP\Filesystem\Internal\Trait\HasConfig;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Exception\FilesystemException;
use PainlessPHP\Filesystem\Interface\DirectoryContentIterator;
use PainlessPHP\Filesystem\Internal\Trait\FiltersFilesystemObjects;

class InternalFilterIterator extends FilterIterator implements DirectoryContentIterator
{
    use HasConfig, FiltersFilesystemObjects;

    private string $path;

    public function __construct(string $path, Iterator $innerIterator, array|DirectoryIteratorConfig $config)
    {
        parent::__construct($innerIterator);
        $this->setPath($path);
        $this->setConfig($config);
    }

    public function accept(): bool
    {
        $file = $this->current();

        if($this->shouldFilter($file, $this->config->resultFilters)) {
            return false;
        }

        return true;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    private function setPath(string $path)
    {
        if(! is_dir($path)) {
            $msg = "Given path '{$path}' is not a directory.";
            throw new FilesystemException($msg);
        }

        $this->path = $path;
    }
}
