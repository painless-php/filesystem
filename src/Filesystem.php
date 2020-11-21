<?php

namespace Nonetallt\Filesystem;

use Nonetallt\Filesystem\Exception\FilesystemException;
use Nonetallt\String\Str;

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
    static private $projectPath;

    /**
     * Path of the current project test dir
     *
     */
    static private $testDirPath;

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
            dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . $file,

            // Path when another package is using this package
            dirname(__DIR__, 3) . DIRECTORY_SEPARATOR . $file,
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
    public static function projectDirectoryPath(string $append = null) : string
    {
        if(static::$projectPath === null) {

            var_dump('foo');
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
    public static function testDirectoryPath(string $append = null) : string
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
            throw new FilesystemException($msg, Filesystem::projectDirectoryPath());
        }

        return static::appendToPath(static::$testDirPath, $append);
    }

    /**
     * Append to the given filesystem path
     *
     */
    public static  function appendToPath(string $path, ?string $append) : string
    {
        if($append === null) {
            return Str::removeSuffix($path, DIRECTORY_SEPARATOR);
        }

        return Str::addSuffix($path, DIRECTORY_SEPARATOR) . $append;
    }
}