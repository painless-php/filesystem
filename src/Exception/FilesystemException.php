<?php

namespace Nonetallt\Filesystem\Exception;

class FilesystemException extends \Exception
{
    protected $path;

    public function __construct(string $message, string $path = '', int $code = 0, \Exception $previous = null)
    {
        if($path !== '') $message = "$message (filepath: '$path')";
        parent::__construct($message, $code, $previous);
        $this->path = $path;
    }

    public function getPath()
    {
        return $this->path;
    }
}
