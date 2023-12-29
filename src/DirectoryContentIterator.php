<?php

namespace PainlessPHP\Filesystem;

use FilterIterator;
use InvalidArgumentException;
use RecursiveIteratorIterator;
use PainlessPHP\Filesystem\Internal\Trait\HasConfig;
use PainlessPHP\Filesystem\Internal\Iterator\RecursiveFilterIterator;
use PainlessPHP\Filesystem\Internal\Iterator\RecursiveDirectoryIterator;

class DirectoryContentIterator extends FilterIterator
{
    use HasConfig;

    private string $path;

    public function __construct(string $path, array|DirectoryContentIteratorConfiguration $config = [])
    {
        parent::__construct(
            new RecursiveIteratorIterator(
                new RecursiveFilterIterator(
                    new RecursiveDirectoryIterator($path),
                    $path,
                    $config
                )
            )
        );

        $this->setPath($path);
        $this->setConfig($config);
    }

    public function accept(): bool
    {
        $file = $this->current();

        if($this->shouldFilterContent($file)) {
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
            throw new InvalidArgumentException($msg);
        }

        $this->path = $path;
    }

    private function shouldFilterContent(FilesystemObject $filesystemObject) : bool
    {
        return $this->shouldFilter($filesystemObject, $this->config->contentFilters);
    }
}
