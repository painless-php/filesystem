<?php

namespace Nonetallt\File\Exception;

class PermissionException extends FilesystemException
{
    protected $path;

    public function __construct(string $message, string $path = '', int $code = 0, \Exception $previous = null)
    {
        parent::__construct($message, $path, $code, $previous);
    }
}
