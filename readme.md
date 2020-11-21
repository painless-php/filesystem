# file

PHP file manipulation using OOP.

## Public API

#### Core

* Nonetallt\Filesystem\Filesystem
* Nonetallt\Filesystem\FilesystemObject
    * Nonetallt\Filesystem\File
    * Nonetallt\Filesystem\Directory
* Nonetallt\Filesystem\FilesystemPermissions

#### Testing

* Nonetallt\Filesystem\Concern\TestsFiles

#### Exception

* Nonetallt\Filesystem\Exception\FilesystemException
    * Nonetallt\Filesystem\Exception\FileNotFoundException
    * Nonetallt\Filesystem\Exception\PermissionException
    * Nonetallt\Filesystem\Exception\TargetNotDirectoryException
    * Nonetallt\Filesystem\Exception\TargetNotFileException

## TODO

* refactor 
    * FileLineIterator 
        * seekable iterator or generator?
    * FileLine
        * write method kind of sucks?

* File
    * implement iteratorAggregate, return generator to replace
      filelineiterator?

* cleanup
    * go through methods and make sure there are permission checks when applicable
    * use file's real path for exceptions?
