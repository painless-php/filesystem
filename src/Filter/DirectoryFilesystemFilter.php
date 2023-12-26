<?php

namespace PainlessPHP\Filesystem\Filter;

use PainlessPHP\Filesystem\Contract\FilesystemFilter;
use PainlessPHP\Filesystem\FilesystemObject;

class DirectoryFilesystemFilter implements FilesystemFilter
{
    public function shouldPass(FilesystemObject $filesystemObject): bool
    {
        return $filesystemObject->isDir();
    }
}
