# file

PHP file manipulation using OOP.

## Public API

#### Core

* Nonetallt\File\File
* Nonetallt\File\FilesystemPermissions

#### Testing

* Nonetallt\File\Concern\TestsFiles

#### Exception

* Nonetallt\File\Exception\FilesystemException
    * Nonetallt\File\Exception\FileNotFoundException
    * Nonetallt\File\Exception\PermissionException
    * Nonetallt\File\Exception\TargetNotDirectoryException
    * Nonetallt\File\Exception\TargetNotFileException

## TODO

* refactor 
    * FileLineIterator 
        * seekable iterator or generator?
    * FileLine
        * write method kind of sucks?

* add "rename" for sake of convenience


* File
    * implement iteratorAggregate, return generator to replace filelineiterator

* cleanup
    * go through methods and make sure there are permission checks when applicable
    * use file's real path for exceptions?
    * rename package to "Nonetallt\Filesystem" ?
