<?php

namespace Nonetallt\File;

use Nonetallt\File\FilesystemObject;
use Nonetallt\File\Exception\PermissionException;
use Nonetallt\File\Exception\FileNotFoundException;
use Nonetallt\File\Exception\TargetNotDirectoryException;
use Nonetallt\File\Exception\FilesystemException;

class Directory extends FilesystemObject
{
    /**
     * Create directory on the filesystem
     *
     */
    public function create(bool $recursive = false)
    {
        // Do not attempt creation if file already exists
        if(file_exists($this->pathname)) {
            return;
        }

        // Check if parent directory exists, if not, create it recursively
        $parentDir = dirname($this->pathname);

        if(! file_exists($parentDir)) {
            if(! $recursive) {
                $msg = "Can't create missing parent directory, recursive option not enabled";
                throw new FilesystemException($msg, $parentDir);
            }

            (new Directory($parentDir))->create(true);
        }

        if(! is_writable($parentDir)) {
            $msg = "Can't create directory, no write permission";
            throw new PermissionException($msg, $parentDir);
        }

        mkdir($this->pathname);
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
        $size = filesize($this->pathname);

        foreach($this->getChildren() as $child)  {
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

        foreach($this->getChildren() as $object) {
            $object->copy($destination . DIRECTORY_SEPARATOR . basename($object->getPathname()));
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
     * Get the path of the object
     *
     * For directories, this is the same as getPathname()
     *
     */
    public function getPath() : string
    {
        return $this->pathname;
    }

    /**
     * Delete filesystem object
     *
     */
    public function delete(bool $recursive = false)
    {
        if(! $this->isEmpty() && ! $recursive) {
            $msg = "Directory is not empty, please use the recursive parameter if you wish to delete the directory along with it's contents";
            throw new FilesystemException($msg, $this->pathname);
        }

        $this->deleteContents(true);
        rmdir($this->pathname);
    }

    /**
     * Delete contents of the object.
     *
     * @param bool $recursive 
     e
     */
    public function deleteContents(bool $recursive = false)
    {
        foreach($this->getChildren() as $object) {
            if($object->isFile() || ($object->isDirectory() && $recursive)) {
                $object->delete(true);
            }
        }
    }

    /**
     * Check if the directory is empty
     *
     */
    public function isEmpty() : bool
    {
        if(! $this->exists()) {
            throw new FileNotFoundException($this->pathname);
        }

        if(! $this->isDirectory()) {
            throw new TargetNotDirectoryException($this->pathname);
        }

        return ! (new \FilesystemIterator($this->pathname))->valid();
    }

    /**
     * Get children filesystem objects
     *
     */
    public function getChildren() : array
    {
        $children = [];

        foreach(array_diff(scandir($this->pathname), ['.', '..']) as $relativePath) {
            $realPath = $this->pathname . DIRECTORY_SEPARATOR . $relativePath;
            $child = is_file($realPath) ? new File($realPath) : new Directory($realPath);
            $children[] = $child;
        }

        return $children;
    }
}

