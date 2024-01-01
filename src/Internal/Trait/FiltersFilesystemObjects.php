<?php

namespace PainlessPHP\Filesystem\Internal\Trait;

use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\Interface\FilesystemFilter;

trait FiltersFilesystemObjects
{
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
