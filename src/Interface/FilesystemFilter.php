<?php

namespace PainlessPHP\Filesystem\Interface;

use PainlessPHP\Filesystem\FilesystemObject;

interface FilesystemFilter
{
    public function shouldPass(FilesystemObject $filesystemObject) : bool;
}
