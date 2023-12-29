<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\FilesystemException;
use PainlessPHP\Filesystem\Exception\FilesystemPermissionException;
use PainlessPHP\Filesystem\Filter\FileFilesystemFilter;
use PainlessPHP\Filesystem\Internal\StringHelpers;

/**
 * Class for filesystem helper functions
 *
 */
class Filesystem
{
    /**
     * Append to the given filesystem path
     *
     */
    public static function appendToPath(string $path, string ...$append) : string
    {
        $path = StringHelpers::removeSuffix($path, DIRECTORY_SEPARATOR);

        foreach($append as $appended) {
            $appended = StringHelpers::removePrefix($appended, DIRECTORY_SEPARATOR);
            $appended = StringHelpers::removeSuffix($appended, DIRECTORY_SEPARATOR);
            $path = $path . DIRECTORY_SEPARATOR . $appended;
        }

        return $path;
    }

    /**
     * Get the home directory of the current user
     *
     * @throws FilesystemException
     *
     */
    public static function homeDirectoryPath(?string $append = null) : string
    {
        $home = getenv('HOME');

        if($home === false) {
            $user = get_current_user();
            $msg = "Could not resolve home directory of user '{$user}'";
            throw new FilesystemException($msg);
        }

        return static::appendToPath($home, $append);
    }

    /**
     * Find a filesystem object with the specified name, starting from the provided path
     * and looking up through the parent directories
     *
     */
    public static function findUpwards(FilesystemObject|string $startPath, string $target) : FilesystemObject
    {
        if(is_string($startPath)) {
            $startPath = FilesystemObject::createFromPath($startPath);
        }

        if(! $startPath->isReadable()) {
            $msg = "Path '$startPath' is not readable";
            throw new FilesystemPermissionException($msg);
        }

        if($startPath->isFile()) {
            $startPath = $startPath->getParentDirectory();
        }

        /** @var Directory $startPath */
        $config = new DirectoryContentIteratorConfiguration(
            scanFilters: [
                fn(FilesystemObject $obj) => $obj->isFile()
            ]
        );

        foreach($startPath->getContents($config) as $filesystemObject) {
            if($filesystemObject->getFilename() === $target) {
                return $filesystemObject;
            }
        }

        if($startPath->isRoot()){
            $msg = "Could not find file '$target', searched up to filesystem root";
            throw new FileNotFoundException($msg);
        }

        return self::findUpwards($startPath->getParentDirectory(), $target);
    }

    /**
     * Find a filesystem object with the specified name, starting from the provided path
     * and looking down through the child directories
     *
     */
    public static function findDownwards(FilesystemObject|string $startPath, string $target) : FilesystemObject
    {
        if(is_string($startPath)) {
            $startPath = FilesystemObject::createFromPath($startPath);
        }

        if(! $startPath->isReadable()) {
            $msg = "Path '$startPath' is not readable";
            throw new FilesystemPermissionException($msg);
        }

        if($startPath->isFile()) {
            $startPath = $startPath->getParentDirectory();
        }

        /** @var Directory $startPath */
        foreach($startPath->getIterator() as $filesystemObject) {
            if($filesystemObject->getFilename() === $target) {
                return $filesystemObject;
            }
        }

        $msg = "Could not find file '$target' inside '$startPath'";
        throw new FileNotFoundException($msg);
    }
}
