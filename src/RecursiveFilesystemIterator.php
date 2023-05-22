<?php

namespace PainlessPHP\Filesystem;

use FilesystemIterator;
use InvalidArgumentException;
use Iterator;
use RecursiveDirectoryIterator;
use SplFileInfo;

class RecursiveFilesystemIterator implements Iterator
{
    private string $path;
    private int $currentKey;
    private string $currentDir;
    private RecursiveDirectoryIterator $currentIterator;
    private bool $skipNext;
    private array $dirsToScan;
    private bool $valid;
    private bool $returnMapping;

    public function __construct(string $path, bool $returnMapping = false)
    {
        $this->setPath($path);
        $this->rewind();
        $this->returnMapping = $returnMapping;
    }

    public function getPath() : string
    {
        return $this->path;
    }

   /**
    * Set the directory path of the iterator
    *
    * @param string $path
    *
    */
    private function setPath(string $path)
    {
        if(! is_dir($path)) {
            $msg = "Given path '$path' is not a directory.";
            throw new InvalidArgumentException($msg);
        }

        $this->path = $path;
    }

    public function current(): FilesystemObject|array
    {
        $current = FilesystemObject::createFromPath($this->getCurrentIterator()->current());

        /* while($current->getBasename() === '.' || $current->getBasename() === '..') { */
        /*     $current = $this->skipFile(); */
        /* } */
        $fullPath = $current->getPathname();

        if($current->isDir()) {
            $this->dirsToScan[$fullPath] = $fullPath;
        }

        If($this->returnMapping) {
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
            ++$this->currentKey;
        }
    }

    public function rewind(): void
    {
        $this->currentDir = $this->path;
        $this->currentKey = 0;
        $this->skipNext = false;
        $this->dirsToScan = [];
        $this->valid = true;
    }

    public function valid(): bool
    {
        return $this->valid && $this->getCurrentIterator()->valid();
    }

    private function skipFile() : SplFileInfo
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

    private function getCurrentIterator() : RecursiveDirectoryIterator
    {
        $this->currentIterator = $this->currentIterator ?? new RecursiveDirectoryIterator($this->path, FilesystemIterator::SKIP_DOTS);

        while(! $this->currentIterator->valid()) {
            $next = $this->getNextIterator();
            if($next === null) break;
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
}
