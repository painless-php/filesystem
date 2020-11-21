<?php 

namespace Nonetallt\Filesystem;

use Nonetallt\File\Exception\FilesystemException;

/**
 * Meta object describing all filesystem entities. The object itself only
 * describes the objects path in the filesystem and does not necessarily exist
 * on the filesystem.
 *
 * - Files
 * - Directories
 *
 */
abstract class FilesystemObject
{
    protected $pathname;

    public function __construct(string $pathname)
    {
        $this->pathname = $pathname;
    }

    /**
     * Create either a file or a directory from the given path based on which
     * object the path corresponds to on the filesystem
     *
     */
    public static function fromPath(string $pathname) : self
    {
        if(is_file($pathname)) {
            return new File($pathname);
        }

        if(is_dir($pathname)) {
            return new Directory($pathname);
        }

        $msg = 'Filesystem object with the given path does not exist';
        throw new FilesystemException($msg, $pathname);
    }

    /**
     * Check if this object is a directory
     *
     */
    public function isDirectory() : bool
    {
        return is_dir($this->pathname);
    }

    /**
     * Check if this object is a file
     *
     */
    public function isFile() : bool
    {
        return is_file($this->pathname);
    }

    /**
     * Get permission for the filesystem object
     *
     */
    public function getPermissions() : FilesystemPermissions
    {
        return new FilesystemPermissions($this->pathname);
    }

    /**
     * Get the path with the name of the object
     *
     */
    public function getPathname() : string
    {
        return $this->pathname;
    }

    /**
     * Get the real path of the object
     *
     */
    public function getRealPath() : string
    {
        return realpath($this->pathname);
    }

    /**
     * Get name of the object
     *
     */
    public function getName() : string
    {
        return basename($this->pathname);
    }

    /**
     * Get the parent directory of this object
     *
     * @return Directory|null The parent directory or null if the object
     * doesn't have a parent directory (root of the filesystem)
     *
     */
    public function getParentDirectory() : ?Directory
    {
        return new Directory(dirname($this->pathname));
    }

    /**
     * Check if this object actually exists in the filesystem
     *
     */
    abstract public function exists() : bool;

    /**
     * Get the size of the object in filesystem in bytes
     *
     * @return int $size filesize in bytes
     *
     */
    abstract public function getSize() : int;

    /**
     * Create the object on the filesystem
     *
     * @param bool $recursive Whether parent directories should be created
     * recursively when creating this object. If parent directory does not
     * exist and recurisive is set to false, a FilesystemException should be
     * thrown
     *
     */
    abstract public function create(bool $recursive = false);

    /**
     * Copy the filesystem object
     *
     */
    abstract public function copy(string $destination);

    /**
     * Move the filesystem object
     *
     * If you don't need to move the object between directories, you can use
     * the "rename" functionality instead
     *
     */
    abstract public function move(string $destination);

    /**
     * Get the path of the object
     *
     * See also getPathname()
     *
     */
    abstract public function getPath() : string;

    /**
     * Delete filesystem object
     *
     */
    abstract public function delete();

    /**
     * Delete contents of the object.
     *
     */
    abstract public function deleteContents();

    /**
     * Check if the object is empty
     *
     */
    abstract public function isEmpty() : bool;

    /**
     * Rename the filesystem object. Provides a convenient way to quickly
     * change the object's name without requiring a precise destination path
     * like move
     *
     */
    abstract public function rename(string $newName);
}
