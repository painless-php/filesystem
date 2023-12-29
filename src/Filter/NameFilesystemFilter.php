<?php

namespace PainlessPHP\Filesystem\Filter;

use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\FilesystemObject;

class NameFilesystemFilter implements FilesystemFilter
{
    public function __construct(private string $name)
    {
    }

    public function shouldPass(FilesystemObject $filesystemObject): bool
    {
        return $filesystemObject->getFilename() !== $this->name;
    }
}
