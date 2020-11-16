<?php

namespace Nonetallt\File;

use Nonetallt\File\Contract\FilesystemObjectInterface;
use Nonetallt\File\Exception\PermissionException;

class Directory implements FilesystemObjectInterface
{
    /**
     * Get the size of the objec in filesystem in bytes
     *
     * @return int $size filesize in bytes
     *
     */
    public function getSize() : int
    {
        // TODO ?
        filesize($this->pathname);
    }

    /**
     * Copy the filesystem object
     *
     */
    public function copy(string $destination)
    {
        copy($this->pathname, $destination);
    }

    /**
     * Move the filesystem object
     *
     */
    public function move(string $destination)
    {
        $this->copy($destination);
        $this->delete();
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
    public function delete()
    {
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
        $objects = array_diff(scandir($this->pathname), ['.', '..']);

        foreach($objects as $object) {
            if($object->isFile() || ($object->isDirectory() && $recursive)) {
                $object->delete();
            }
        }
    }
}

