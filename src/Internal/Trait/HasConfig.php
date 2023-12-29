<?php

namespace PainlessPHP\Filesystem\Internal\Trait;

use PainlessPHP\Filesystem\DirectoryContentIteratorConfiguration;
use PainlessPHP\Filesystem\FilesystemObject;

trait HasConfig
{
    private DirectoryContentIteratorConfiguration $config;

    private function setConfig(array|DirectoryContentIteratorConfiguration $config)
    {
        if(is_array($config)) {
            $config = new DirectoryContentIteratorConfiguration(...$config);
        }

        $this->config = $config;
    }

    public function getConfiguration() : DirectoryContentIteratorConfiguration
    {
        return $this->config;
    }

	/**
     * @param FilesystemObject $filesystemObject
     * @param array<FilesystemFilter> $filters
     *
     * @return bool
     */
    private function shouldFilter(FilesystemObject $filesystemObject, array $filters) : bool
    {
        foreach($filters as $filter) {
            if(! $filter->shouldPass($filesystemObject)) {
                return true;
            }
        }

        return false;
    }
}
