<?php

namespace Nonetallt\File\Exception;

/**
 * Should be thrown when target needs to be a file but path refers to
 * a directory or does not exist
 */
class TargetNotFileException extends FilesystemException
{
    public function __construct(string $path, string $message = "Target must be a file", int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $path, $code, $previous);
    }
}
