<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;

/**
 * Class for filesystem helper functions
 *
 */
class Filesystem
{
    /**
     * Path of the current project
     *
     */
    static private ?string $projectPath;

    /**
     * Path of the current project test dir
     *
     */
    static private ?string $testDirPath;

    /**
     * Get the composer autoloader class from autoload file
     *
     * @throws FilesystemException
     *
     */
    public static function composerAutoloaderPath() : string
    {
        $file = 'autoload.php';

        $choices = [
            // Path when used by this package
            dirname(__DIR__) . "/vendor/{$file}",

            // Path when another package is using this package
            dirname(__DIR__, 3) . "/{$file}",
        ];

        foreach ($choices as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        $msg = 'Unable to locate class composer autoloader';
        throw new FilesystemException($msg);
    }

    /**
     * Get the project path using lazy loading
     *
     * @throws FilesystemException
     *
     */
    public static function projectDirectoryPath(?string $append = null) : string
    {
        if(static::$projectPath === null) {
            try {
                $autoloaderPath = static::composerAutoloaderPath();
                static::$projectPath = dirname($autoloaderPath, 2);
            }
            catch(FilesystemException $e) {
                $msg = "Composer project root could not be determined";
                throw new FilesystemException($msg, 0, $e);
            }
        }

        return static::appendToPath(static::$projectPath, $append);
    }

    /**
     * Get the test path using lazy loading
     *
     * @throws FilesystemException
     *
     */
    public static function testDirectoryPath(?string $append = null) : string
    {
        $choices = ['test', 'tests'];

        if(static::$testDirPath === null) {

            foreach($choices as $dir) {
                $path = static::projectDirectoryPath($dir);
                if(is_dir($path)) {
                    static::$testDirPath = $path;
                    break;
                }
            }
        }

        if(static::$testDirPath === null) {
            $choices = implode(PHP_EOL, $choices);
            $msg = "Could not find a valid testing directory from following choices:" . PHP_EOL . $choices;
            throw new FilesystemException($msg, self::projectDirectoryPath());
        }

        return static::appendToPath(static::$testDirPath, $append);
    }

    /**
     * Append to the given filesystem path
     *
     */
    public static function appendToPath(string $path, string $append = '') : string
    {
        if(empty($append)) {
            return str_ends_with($path, DIRECTORY_SEPARATOR) ? mb_substr($path, 0, -1) : $path;
        }

        if(! str_ends_with($path, DIRECTORY_SEPARATOR)) {
            $path .= DIRECTORY_SEPARATOR;
        }

        if(! str_starts_with($append, DIRECTORY_SEPARATOR)) {
            $append = mb_substr($append, 1);
        }

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
