<?php

namespace PainlessPHP\Filesystem\Contract;

use PainlessPHP\Filesystem\FilesystemObject;

interface FilesystemFilter
{
    public function shouldPass(FilesystemObject $filesystemObject) : bool;
}
