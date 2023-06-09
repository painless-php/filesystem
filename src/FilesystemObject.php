<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;
use SplFileInfo;

/**
 * Meta object describing all filesystem entities. The object itself only
 * describes the objects path in the filesystem and does not necessarily exist
 * on the filesystem.
 *
 * - Files
 * - Directories
 *
 */
abstract class FilesystemObject extends SplFileInfo
{
    public function __construct(string|SplFileInfo $pathname)
    {
        parent::__construct(is_string($pathname) ? $pathname : $pathname->getPathname());
    }

    /**
     * Create either a file or a directory from the given path based on which
     * object the path corresponds to on the filesystem
     *
     */
    public static function createFromPath(string $pathname) : self
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
     * Alias for SplFileInfo::isDir()
     *
     */
    public function isDirectory() : bool
    {
        return $this->isDir();
    }

    /**
     * Get the real path of the object. This method doesn't use the native
     * realpath() function because realpath() won't work with files that don't exist.
     *
     */
    public function getAbsolutePath() : string
    {
		$path = str_replace('\\', '/', $this->getPathname());

        if(str_starts_with($path, '~')) {
            $pathAfterHome = substr($path, 1);

            if($pathAfterHome !== '' && ! str_ends_with($pathAfterHome, DIRECTORY_SEPARATOR)) {
                $pathAfterHome .= DIRECTORY_SEPARATOR;
            }

            $path = Filesystem::homeDirectoryPath() . $pathAfterHome;
        }

        if (!str_contains($path, '..')) {
            return $path;
        }

        $first = '';
        $parts = explode('/', $path);
        if ($filter) {
            $first = $path[0] === '/' ? '/' : '';
            $parts = array_filter($parts, 'strlen');
        }

        $absolutes = [];
        foreach ($parts as $part) {
            if ($part === '.') {
                continue;
            }

            if ($part === '..') {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }

        return $first . implode('/', $absolutes);
    }

    /**
     * Get the path of this object in relation to another path. An exception
     * will be thrown If the given path is not a parent path of this object
     *
     * @throws FilesystemException
     *
     */
    public function getRelativePath(string $parentPath) : string
    {
        $path = $this->getAbsolutePath();
        $parentPath = str_ends_with($parentPath, DIRECTORY_SEPARATOR) ? $parentPath : $parentPath . DIRECTORY_SEPARATOR;

        if(! str_starts_with($path, $parentPath)) {
            $msg = "Path '{$path}' is not relative to parent path '{$parentPath}'";
            throw new FilesystemException($msg);
        }

        return mb_substr($path, mb_strlen($parentPath));
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
        return new Directory(dirname($this->getPathname()));
    }

    /**
     * Get the path of the object
     *
     * Alias for getPathname()
     *
     */
    public function getPath() : string
    {
        return $this->getPathname();
    }

    /**
     * Check if this object actually exists in the filesystem
     *
     */
    abstract public function exists() : bool;

    /**
     * Create the object on the filesystem
     *
     * @param bool $recursive Whether parent directories should be created
     * recursively when creating this object. If parent directory does not
     * exist and recurisive is set to false, a FilesystemException should be
     * thrown
     *
     */
    abstract public function create(bool $recursive = false, bool $overwrite = false);

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
