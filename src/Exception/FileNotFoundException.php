<?php

namespace PainlessPHP\Filesystem\Exception;

use Exception;

class FileNotFoundException extends FilesystemException
{
    public static function createFromPath(string $filepath, int $code = 0, Exception $previous = null) : self
    {
        return new self("File not found (filepath: '{$filepath}')", $code, $previous);
    }
}
