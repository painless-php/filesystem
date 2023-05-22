<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\FilesystemPermissionException;
use IteratorAggregate;

class File extends FilesystemObject implements IteratorAggregate
{
    /**
     * Create a new temporary file
     *
     */
    public static function createTemporary() : self
    {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);

        return new self($meta['uri']);
    }

    /**
     * Create the file on the filesystem
     *
     */
    public function create(bool $recursive = false)
    {
        if($recursive) {
            $this->getParentDirectory()->create(true);
        }

        file_put_contents($this->getPathname(), '');
    }

    /**
     * Delete the file on the filesystem
     *
     */
    public function delete()
    {
        unlink($this->getPathname());
    }

    /**
     * Delete contents of the file
     *
     */
    public function deleteContents()
    {
        file_put_contents($this->getPathname(), '');
    }

    /**
     * Check if the file has a given extension or no extension at all
     *
     */
    public function hasExtension(string $extension = null) : bool
    {
        $realExtension = $this->getExtension();

        /* File has extension if it is not empty */
        if($extension === null) {
            return $realExtension !== null;
        }

        /* Remove leading dots for comparison */
        while(str_starts_with($extension, '.')) {
            $extension = substr($extension, 1);
        }
        /* Check if the file extension equals the one given by user */
        return $this->getExtension() === $extension;
    }

    /**
     * @throws FilesystemException
     *
     * @param FilesystemStreamMode $mode fopen() mode
     * @return resource $stream
     *
     */
    public function openStream(FilesystemStreamMode $mode)
    {
        if(! $this->exists()) {
            throw FileNotFoundException::createFromPath($this->getPathname());
        }

        $stream = fopen($this->getPathname(), $mode->value);

        if($stream === false) {
            $msg = "Could not open stream (filepath '{$this->getPathname()}')";
            throw new FilesystemException($msg);
        }

        return $stream;
    }

    /**
     * @return int $size filesize in bytes
     *
     */
    public function getSize() : int
    {
        if(! $this->exists()) {
            throw FileNotFoundException::createFromPath($this->getPathname());
        }

        return filesize($this->getPathname());
    }

    public function getLines() : FileLineIterator
    {
        return new FileLineIterator($this);
    }

    public function getPath() : string
    {
        return dirname($this->getPathname());
    }

    public function getContents() : string
    {
        return file_get_contents($this->getPathname());
    }

    public function getIterator() : \Traversable
    {
        return new FileLineIterator($this);
    }

    /**
     * @param string|File|FileLineIterator $content
     *
     */
    public function write(string|File|FileLineIterator $content)
    {
        if(is_string($content)) {
            file_put_contents($this->getPathname(), $content);
            return;
        }

        $this->writeLines($content instanceof File ? $content->getLines() : $content);
    }

    public function writeLines(FileLineIterator $lines)
    {
        $stream = $this->openStream(FilesystemStreamMode::Write);

        foreach($lines as $line) {
            fwrite($stream, $line->getContent());
        }

        fclose($stream);
    }

    public function copy(string $destinationPath)
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->getPathname());
        }

        if(! $this->isReadable()) {
            $path = $this->getRealPath();
            $msg = "Copy source '$path' is not readable";
            throw new FilesystemPermissionException($msg);
        }

        if(is_writable($destinationPath)) {
            $msg = "Copy destination '$destinationPath' is not writable";
            throw new FilesystemPermissionException($msg);
        }

        copy($this->getPathname(), $destinationPath);
    }

    public function move(string $destination)
    {
        $this->copy($destination);
        unlink($this->getPathname());
    }

    public function rename(string $name)
    {
        $this->move($this->getPath() . "/$name");
    }

    public function isEmpty() : bool
    {
        return $this->getSize() === 0;
    }

    public function exists() : bool
    {
        return is_file($this->getPathname());
    }
}
