<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemPermissionException;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\FilesystemException;
use FilesystemIterator;
use PainlessPHP\Filesystem\Interface\DirectoryContentIterator;
use PainlessPHP\Filesystem\DirectoryIterator;
use PainlessPHP\Filesystem\RecursiveDirectoryIterator;

class Directory extends FilesystemObject
{
    public static function createFromPath(string $pathname): self
    {
        if(is_file($pathname)) {
            $msg = "Target path '$pathname' is a file";
            throw new FilesystemException($msg);
        }

        if(! file_exists($pathname)) {
            throw FileNotFoundException::createFromPath($pathname);
        }

        return new self($pathname);
    }

    /**
     * Create directory on the filesystem
     *
     */
    public function create(bool $recursive = false, bool $overwrite = false) : bool
    {
        $destination = $this->getAbsolutePath();

        // Do not attempt creation if file already exists
        if(is_dir($destination)) {
            return true;
        }

        if(is_file($destination)) {
            $msg = "A file with this name already exists";
            throw new FilesystemException($msg, $destination);
        }

        // Check if parent directory exists, if not, create it recursively
        $parentDir = dirname($destination);

        if(! is_dir($parentDir)) {

            if(! $recursive) {
                $msg = "Can't create missing parent directory, recursive option not enabled";
                throw new FilesystemException($msg, $parentDir);
            }

            if(is_file($parentDir)) {
                $msg = "Can't create missing parent directory, file with this name already exists";
                throw new FileNotFoundException($msg, $parentDir);
            }

            (new self($parentDir))->create(true);
        }

        if(! is_writable($parentDir)) {
            $msg = "Can't create directory, no write permission";
            throw new FilesystemPermissionException($msg, $parentDir);
        }

        return mkdir($destination);
    }

    /**
     * Get the size of the object in filesystem in bytes. This method returns
     * the size of the directory as well as all directories and files contained
     * within. If you want to get the size of the directory node object
     * instead, just call php filesize() function on the path
     *
     * @return int $size filesize in bytes
     *
     */
    public function getSize() : int
    {
        $size = filesize($this->getPathname());

        foreach($this as $child)  {
            $size += $child->getSize();
        }

        return $size;
    }

    /**
     * Copy the directory to target destination
     *
     */
    public function copy(string $destination, bool $recursive = false, bool $createRecursively = false, DirectoryIteratorConfig|array $config = []) : bool
    {
        if(! (new self($destination))->create(recursive: $createRecursively)) {
            return false;
        }

        if($recursive) {
            return $this->copyContents(
                destination: $destination,
                recursive: $recursive,
                config: $config
            );
        }

        return true;
    }

    /**
     * Copy the contents of the directory to the target location
     *
     */
    public function copyContents(string $destination, bool $recursive = false, DirectoryIteratorConfig|array $config = []) : bool
    {
        foreach($this->getIterator(recursive: false, config: $config) as $object) {
            $fileDestination = Filesystem::appendToPath($destination, $object->getFilename());

            if($recursive && ! $object->copy($fileDestination, $recursive, false, $config)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Move the filesystem object to target destination
     *
     */
    public function move(string $destination, bool $recursive = false)
    {
        $this->copy($destination, $recursive);
        $this->delete($recursive);
    }

    /**
     * Delete the directory
     *
     */
    public function delete(bool $recursive = false, DirectoryIteratorConfig|array $config = []) : bool
    {
        $deleted = true;

        if($recursive) {
            $deleted = $this->deleteContents(recursive: $recursive, config: $config);
        }

        if($deleted && $this->isEmpty()) {
            return rmdir($this->getPathname());
        }

        return false;
    }

    /**
     * Delete contents of the directory
     *
     */
    public function deleteContents(bool $recursive = false, DirectoryIteratorConfig|array $config = []) : bool
    {
        $deleted = true;

        foreach($this->getIterator(recursive: false, config: $config) as $object) {

            if($recursive && ! $object->delete($recursive, $config)) {
                $deleted = false;
            }
        }

        return $deleted;
    }

    /**
     * Check if the directory is empty
     *
     */
    public function isEmpty() : bool
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->getPathname());
        }

        return ! (new FilesystemIterator($this->getPathname()))->valid();
    }

    /**
     * Check if the directory contains the target with a relative path
     *
     */
    public function containsRelativePath(string $relativePath, bool $recursive = false) : bool
    {
        foreach($this->getIterator(recursive: $recursive) as $object) {
            if($relativePath === $object->getRelativePath($this->getPathname())) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get children filesystem objects
     *
     * @param bool $recursive
     * @param DirectoryIteratorConfig|array $config
     *
     * @return array<FilesystemObject> $contents
     *
     */
    public function getContents(bool $recursive = false, DirectoryIteratorConfig|array $config = []) : array
    {
        $children = [];
        $iterator = $this->getIterator($recursive, $config);

        foreach($iterator as $item) {
            $children[] = $item;
        }

        return $children;
    }

    /**
     * Check if this directory exists on the filesystem
     *
     */
    public function exists() : bool
    {
        return is_dir($this->getPathname());
    }

    /**
     * Rename the directory
     *
     */
    public function rename(string $newName)
    {
        $this->move(dirname($this->getPathname()) . "/{$newName}", true);
    }

    /**
     * Get an iterator for the directory contents
     *
     */
    public function getIterator(bool $recursive = false, DirectoryIteratorConfig|array $config = []): DirectoryContentIterator
    {
        if($recursive) {
            return new RecursiveDirectoryIterator($this->getAbsolutePath(), $config);
        }

        return new DirectoryIterator($this->getAbsolutePath(), $config);
    }

    public function findFirstFromContents(Callable $filter, bool $recursive = false,  DirectoryIteratorConfig|array $config = []) : ?FilesystemObject
    {
        foreach ($this->getIterator(config: $config, recursive: $recursive) as $file) {
            if($filter($file)) {
                return $file;
            }
        }

        return null;
    }
}
