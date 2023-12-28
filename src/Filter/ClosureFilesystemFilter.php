<?php

namespace PainlessPHP\Filesystem\Filter;

use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\FilesystemObject;
use Closure;

class ClosureFilesystemFilter implements FilesystemFilter
{
    public function __construct(private Closure $closure)
    {
    }

    public function shouldPass(FilesystemObject $filesystemObject): bool
    {
        return ($this->closure)($filesystemObject);
    }
}
