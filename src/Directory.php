<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemPermissionException;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\FilesystemException;
use FilesystemIterator;

class Directory extends FilesystemObject
{
    /**
     * Create directory on the filesystem
     *
     */
    public function create(bool $recursive = false)
    {
        // Do not attempt creation if file already exists
        if(is_dir($this->getPathname())) {
            return;
        }
        else if(is_file($this->getPathname())) {
            $msg = "A file with this name already exists";
            throw new FilesystemException($msg, $this->getPathname());
        }

        // Check if parent directory exists, if not, create it recursively
        $parentDir = dirname($this->getPathname());

        if(! is_dir($parentDir)) {

            if(! $recursive) {
                $msg = "Can't create missing parent directory, recursive option not enabled";
                throw new FilesystemException($msg, $parentDir);
            }

            if(is_file($parentDir)) {
                $msg = "Can't create missing parent directory, file with this name already exists";
                throw new FileNotFoundException($msg, $parentDir);
            }

            (new Directory($parentDir))->create(true);
        }

        if(! is_writable($parentDir)) {
            $msg = "Can't create directory, no write permission";
            throw new FilesystemPermissionException($msg, $parentDir);
        }

        mkdir($this->getPathname());
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

        foreach($this->getContents() as $child)  {
            $size += $child->getSize();
        }

        return $size;
    }

    /**
     * Copy the filesystem object
     *
     */
    public function copy(string $destination, bool $recursive = false)
    {
        (new Directory($destination))->create($recursive);

        foreach($this->getContents() as $object) {
            $object->copy("$destination/" . basename($object->getPathname()()));
            $object->delete();
        }
    }

    /**
     * Move the filesystem object
     *
     */
    public function move(string $destination, bool $recursive = false)
    {
        $this->copy($destination, $recursive);
        $this->delete(true);
    }

    /**
     * Delete filesystem object
     *
     ** @param bool $recursive Whether subdirectories and their contents should be
     * deleted recursively
     *
     * @param array|string $exclude List of file / directory names to save from
     * deletion
     *
     ** @param string|null $root path to the topmost level, should not be used
     * outside class
     *
     * @return bool $deleted Whether the directory was actually deleted
     * (some files may be spared if $exclude parameter is used)
     *
     */
    public function delete(bool $recursive = false, array $exclude = [], string $root = null) : bool
    {
        if(! $recursive && ! $this->isEmpty()) {
            $msg = "Directory is not empty, please use the recursive parameter if you wish to delete the directory along with it's contents";
            throw new FilesystemException($msg, $this->getPathname());
        }

        if($this->deleteContents(true, $exclude)) {
            rmdir($this->getPathname());
            return true;
        }

        return false;
    }

    /**
     * Delete contents of the object.
     *
     * @param bool $recursive Whether contents of subdirectories should be
     * deleted recursively
     *
     * @param array|string $exclude List of file / directory names to save from
     * deletion
     *
     * @param string|null $root path to the topmost level, should not be used
     * outside class
     *
     * @return bool $isEmpty Whether directory is empty after deletion (some
     * files may be spared if $exclude parameter is used)
     e
     */
    public function deleteContents(bool $recursive = false, array $exclude = [], string $root = null) : bool
    {
        if($root === null) {
            $root = $this->getAbsolutePath();
        }

        $isEmpty = true;

        foreach($this->getContents() as $object) {

            // Skip deletion of non recursive dirs
            if(! $recursive && $object->isDirectory()) {
                continue;
            }

            // Skip deletion of excluded files
            if(
                in_array($object->getName(), $exclude) ||
                in_array($object->getRelativePath($root), $exclude) ||
                in_array($object->getAbsolutePath(), $exclude)
            ) {
                $isEmpty = false;
                continue;
            }

            if($object->delete(true, $exclude)) {
                $isEmpty = false;
            }
        }

        return $isEmpty;
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
     * Get children filesystem objects
     *
     */
    public function getContents() : array
    {
        $children = [];

        foreach(array_diff(scandir($this->getPathname()), ['.', '..']) as $relativePath) {
            $realPath = "{$this->getPathname()}/$relativePath";
            $child = is_file($realPath) ? new File($realPath) : new Directory($realPath);
            $children[] = $child;
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
        $this->move(dirname($this->getPathname()) . "/$newName", true);
    }
}
