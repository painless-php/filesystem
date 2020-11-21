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

* Nonetallt\Filesystem\Concern\DeletesTestOutput

#### Exception

* Nonetallt\Filesystem\Exception\FilesystemException
    * Nonetallt\Filesystem\Exception\FileNotFoundException
    * Nonetallt\Filesystem\Exception\PermissionException
    * Nonetallt\Filesystem\Exception\TargetNotDirectoryException
    * Nonetallt\Filesystem\Exception\TargetNotFileException

## Development

Note that this package uses the following env variables for testing:
* REAL_HOME

## TODO

* Test
    * getAbsolutePath() conversions
    * directory delete relative and absolute exclusion patterns

* refactor 
    * FileLineIterator 
        * seekable iterator or generator?
    * FileLine
        * write method kind of sucks?

* File
    * implement iteratorAggregate, return generator to replace
      filelineiterator?

* Directory
    * allow deletion exclusion by relative path and absolute path

* cleanup
    * go through methods and make sure there are permission checks when applicable
    * use file's real path for exceptions?
