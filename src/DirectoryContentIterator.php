<?php

namespace PainlessPHP\Filesystem;

use FilesystemIterator;
use InvalidArgumentException;
use Iterator;
use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use RecursiveDirectoryIterator;
use SplFileInfo;

class DirectoryContentIterator implements Iterator
{
    private string $path;
    private int $currentKey;
    private RecursiveDirectoryIterator $currentIterator;
    private bool $skipNext;
    private array $dirsToScan;
    private bool $valid;
    private DirectoryContentIteratorConfiguration $config;

    public function __construct(string $path, array|DirectoryContentIteratorConfiguration $config = [])
    {
        $this->setPath($path);
        $this->rewind();
        $this->setConfig($config);
    }

    private function setConfig(array|DirectoryContentIteratorConfiguration $config)
    {
        if(is_array($config)) {
            $config = new DirectoryContentIteratorConfiguration(...$config);
        }

        $this->config = $config;
    }

    public function getPath() : string
    {
        return $this->path;
    }

    private function setPath(string $path)
    {
        if(! is_dir($path)) {
            $msg = "Given path '{$path}' is not a directory.";
            throw new InvalidArgumentException($msg);
        }

        $this->path = $path;
    }

    public function current(): FilesystemObject|array
    {
        $current = FilesystemObject::createFromPath($this->getCurrentIterator()->current());
        $fullPath = $current->getPathname();

        if($current->isDir() && $this->config->recursive && $this->shouldScan($current)) {
            $this->dirsToScan[$fullPath] = $fullPath;
        }

        if($this->shouldFilterContent($current)) {
            $current = $this->skipCurrent();
        }

        If($this->config->returnMapping) {
            return [$current->getRelativePath(basename($this->path)), $fullPath];
        }

        return $current;
    }

    public function key(): int
    {
        return $this->currentKey;
    }

    public function next(): void
    {
        $this->getCurrentIterator()->next();

        if($this->skipNext) {
            $this->skipNext = false;
        }
        else {
            $this->currentKey++;
        }
    }

    public function rewind(): void
    {
        $this->currentKey = 0;
        $this->skipNext = false;
        $this->dirsToScan = [];
        $this->valid = true;
    }

    public function valid(): bool
    {
        return $this->valid && $this->getCurrentIterator()->valid();
    }

    private function skipCurrent() : SplFileInfo
    {
        $this->skipNext = true;
        $this->next();

        return $this->current();
    }

    public function skipDirectory()
    {
        $next = $this->getNextIterator();

        if($next === null) {
            $this->valid = false;
            return;
        }

        $this->currentIterator = $next;
    }

    public function getConfiguration() : DirectoryContentIteratorConfiguration
    {
        return $this->config;
    }

    private function getCurrentIterator() : RecursiveDirectoryIterator
    {
        $this->currentIterator = $this->currentIterator ?? new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);

        while(! $this->currentIterator->valid()) {
            $next = $this->getNextIterator();
            if($next === null) {
                break;
            }
            $this->currentIterator = $next;
        }

        return $this->currentIterator;
    }

    private function getNextIterator() : ?RecursiveDirectoryIterator
    {
        if(empty($this->dirsToScan)) {
            return null;
        }

        $dirPath = array_shift($this->dirsToScan);
        return new RecursiveDirectoryIterator($dirPath, FilesystemIterator::SKIP_DOTS);
    }

    private function shouldScan(FilesystemObject $filesystemObject) : bool
    {
        return ! $this->runFilters($filesystemObject, $this->config->scanFilters);
    }

    private function shouldFilterContent(FilesystemObject $filesystemObject) : bool
    {
        return $this->runFilters($filesystemObject, $this->config->contentFilters);
    }

    /**
     * @param FilesystemObject $filesystemObject
     * @param array<FilesystemFilter> $filters
     *
     * @return bool
     */
    private function runFilters(FilesystemObject $filesystemObject, array $filters) : bool
    {
        foreach($filters as $filter) {
            if(! $filter->shouldPass($filesystemObject)) {
                return true;
            }
        }

        return false;
    }
}
