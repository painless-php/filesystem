<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\FilesystemPermissionException;
use IteratorAggregate;
use Traversable;

class File extends FilesystemObject implements IteratorAggregate
{
    public static function createFromPath(string $pathname, bool $allowNonexistent = false): self
    {
        if(is_dir($pathname)) {
            $msg = "Target path '$pathname' is a directory";
            throw new FilesystemException($msg);
        }

        if(! $allowNonexistent && ! file_exists($pathname)) {
            throw FileNotFoundException::createFromPath($pathname);
        }

        return new self($pathname);
    }

    /**
     * Create the file on the filesystem
     *
     */
    public function create(bool $recursive = false, bool $overwrite = false) : bool
    {
        if($this->exists() && ! $overwrite) {
            return true;
        }

        if($recursive) {
            $this->getParentDirectory()->create(true);
        }

        file_put_contents($this->getPathname(), '');
    }

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
     * Delete the file on the filesystem
     *
     */
    public function delete() : bool
    {
        return unlink($this->getPathname());
    }

    /**
     * Delete contents of the file
     *
     */
    public function deleteContents()
    {
        // Do not create file with empty contents if file does not exist.
        if($this->exists()) {
            file_put_contents($this->getPathname(), '');
        }
    }

    /**
     * Check if the file has a given extension or no extension at all
     *
     */
    public function hasExtension(?string $extension = null) : bool
    {
        $realExtension = $this->getExtension();

        /* File has extension if it is not empty */
        if($extension === null) {
            return $realExtension !== '';
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
     *  Get the size of the file.
     *
     * @override
     * @throws FileNotFoundException
     *
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

    public function readLines() : FileLineIterator
    {
        return new FileLineIterator($this);
    }

    public function getContents() : string
    {
        return file_get_contents($this->getPathname());
    }

    public function getIterator() : Traversable
    {
        return new FileLineIterator($this);
    }

    /**
     * @param string|File|FileLineIterator $content
     *
     */
    public function write(string|self|FileLineIterator $content)
    {
        if(is_string($content)) {
            file_put_contents($this->getPathname(), $content);
            return;
        }

        $this->writeLines($content instanceof self ? $content->readLines() : $content);
    }

    public function writeLines(FileLineIterator $lines)
    {
        $stream = $this->openStream(FilesystemStreamMode::Write);

        foreach($lines as $line) {
            fwrite($stream, $line->getContent());
        }

        fclose($stream);
    }

    public function copy(string $destination) : bool
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->getPathname());
        }

        if(! $this->isReadable()) {
            $path = $this->getRealPath();
            $msg = "Copy source '{$path}' is not readable";
            throw new FilesystemPermissionException($msg);
        }

        $parentDir = dirname($destination);
        $hasParentDir = $destination !== $parentDir;

        if($hasParentDir && ! is_writable($parentDir)) {
            $msg = "Copy destination '{$destination}' parent directory is not writable";
            throw new FilesystemPermissionException($msg);
        }

        return copy($this->getPathname(), $destination);
    }

    public function move(string $destination)
    {
        $this->copy($destination);
        unlink($this->getPathname());
    }

    public function rename(string $newName)
    {
        $this->move($this->getPath() . "/{$newName}");
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
