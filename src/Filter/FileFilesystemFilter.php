<?php

namespace PainlessPHP\Filesystem\Filter;

use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\FilesystemObject;

class FileFilesystemFilter implements FilesystemFilter
{
    public function shouldPass(FilesystemObject $filesystemObject): bool
    {
        return $filesystemObject->isFile();
    }
}
