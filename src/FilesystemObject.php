<?php 
namespace Nonetallt\File;

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
     * Check if this object actually exists in the filesystem
     *
     */
    public function exists() : bool
    {
        return file_exists($this->pathname);
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
     * Get the size of the objec in filesystem in bytes
     *
     * @return int $size filesize in bytes
     *
     */
    abstract public function getSize() : int;

    /**
     * Create the object on the filesystem
     *
     */

    /**
     * Copy the filesystem object
     *
     */
    abstract public function copy(string $destination) : FilesystemObject;

    /**
     * Move the filesystem object
     *
     * Supersedes "rename" functionality
     *
     */
    abstract public function move(string $destination) : FilesystemObject;

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
}
