<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;
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
    public static function appendToPath(string $path, string $append = '') : string
    {
        if(trim($append) === '') {
            return StringHelpers::removeSuffix($path, DIRECTORY_SEPARATOR);
        }

        $path = StringHelpers::addSuffix($path, DIRECTORY_SEPARATOR);
        $append = StringHelpers::removePrefix($append, DIRECTORY_SEPARATOR);

        return $path . $append;
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
}
