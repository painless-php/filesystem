<?php

namespace Nonetallt\File\Exception;

/**
 * Should be thrown when target needs to be a directory but path refers to
 * a file does not exist
 */
class TargetNotDirectoryException extends FilesystemException
{
    public function __construct(string $path, string $message = "Target must be a directory", int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $path, $code, $previous);
    }
}
