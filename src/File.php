<?php

namespace Nonetallt\File;

use Nonetallt\File\Exception\FilesystemException;
use Nonetallt\File\Exception\FileNotFoundException;
use Nonetallt\File\Exception\TargetNotFileException;
use Nonetallt\File\Exception\PermissionException;
use Nonetallt\File\FilesystemPermissions;
use Nonetallt\String\Str;

class File extends FilesystemObject implements \IteratorAggregate
{
    /**
     * Create the file on the filesystem
     *
     */
    public function create(bool $recursive = false)
    {
        if($recursive) {
            $dir = new Directory(dirname($this->pathname));
            $dir->create(true);
        }

        file_put_contents($this->pathname, '');
    }

    /**
     * Create a new temporary file
     *
     */
    public static function temp() : self
    {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);

        return new self($meta['uri']);
    }

    public function delete()
    {
        // TODO permissions?
        unlink($this->pathname);
    }

    public function deleteContents()
    {
        file_put_contents($this->pathname, '');
    }

    public function exists() : bool
    {
        return file_exists($this->pathname);
    }

    public function hasExtension(string $extension = null) : bool
    {
        $realExtension = $this->getExtension();

        /* File has extension if it is not empty */
        if($extension === null) {
            return $realExtension !== null;
        }

        /* Remove leading dots for comparison */
        while(Str::startsWith($extension, '.')) {
            $extension = substr($extension, 1);
        } 
        /* Check if the file extension equals the one given by user */
        return $this->getExtension() === $extension;
    }
    
    /**
     * @throws Nonetallt\Helpers\Filesystem\Exceptions\FilesystemException
     *
     * @param string $mode fopen() mode
     * @return resource $stream
     *
     */
    public function openStream(string $mode)
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->pathname);
        }

        if($this->isDir()) {
            throw new TargetNotFileException($this->pathname);
        }

        $this->getPermissions()->validateStreamMode($mode);

        $stream = fopen($this->pathname, $mode);

        if($stream === false) {
            $msg = 'Could not open stream';
            throw new FilesystemException($msg, $this->pathname);
        }

        return $stream;
    }

    public function isDir() : bool
    {
        return is_dir($this->pathname);
    }

    public function isFile() : bool
    {
        return is_file($this->pathname);
    }

    /**
     * @return int $size filesize in bytes
     *
     */
    public function getSize() : int
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->pathname);
        }

        return filesize($this->pathname);
    }

    public function getLines() : FileLineIterator
    {
        return new FileLineIterator($this);
    }

    public function getPermissions() : FilesystemPermissions
    {
        return new FilesystemPermissions($this->pathname);
    }

    public function getExtension() : ?string
    {
        $parts = explode('.', $this->pathname);
        $partsCount = count($parts);

        /* File does not have an extension */
        if($partsCount < 2) return null;

        return $parts[$partsCount - 1];
    }

    public function getPath() : string
    {
        return dirname($this->pathname);
    }

    public function getPathname() : string
    {
        return $this->pathname;
    }

    public function getRealPath() : string
    {
        return realpath($this->pathname);
    }

    public function getContent() : string
    {
        return file_get_contents($this->pathname);
    }   

    public function getIterator() : \Traversable
    {
        return new FileLineIterator($this);
    }

    /**
     * string|FileLineIterator|File $content
     *
     *
     */
    public function write($content)
    {
        if(is_string($content)) {
            file_put_contents($this->pathname, $content);
            return;
        }

        $fileClass = File::class;
        if(is_a($content, $fileClass)) {
            $this->writeLines($content->getLines());
            return;
        }

        $iteratorClass = FileLineIterator::class;
        if(is_a($content, $iteratorClass)) {
            $this->writeLines($content);
            return;
        }

        $msg = "Write content must be either a string, $fileClass or $iteratorClass";
        throw new \InvalidArgumentException($msg);
    }

    public function writeLines(FileLineIterator $lines)
    {
        $stream = $this->openStream('w');
        foreach($lines as $line) {
            fwrite($stream, $line->getContent());
        }
        fclose($stream);
    }

    public function copy(string $destination)
    {
        $permissions = new FilesystemPermissions($destination);

        if(! $this->exists()) {
            throw new FileNotFoundException($this->pathname);
        }

        if(! $this->getPermissions()->isReadable()) {
            $path = $this->getRealPath();
            $msg = "Copy source '$path' is not readable";
            throw new PermissionException($msg, $destination);
        }

        if(! $permissions->isWritable()) {
            $msg = "Copy destination '$destination' is not writable";
            throw new PermissionException($msg, $destination);
        }

        // TODO recursive, create destination?
        copy($this->pathname, $destination);
    }

    public function move(string $destination)
    {
        $this->copy($destination);
        unlink($this->pathname);
    }

    public function rename(string $name)
    {
        $this->move($this->getPath() . DIRECTORY_SEPARATOR . $name);
    }

    public function isEmpty() : bool
    {
        return $this->getSize() === 0;
    }
}
