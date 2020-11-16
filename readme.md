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

* use file's real path for exceptions
* FileLineIterator : seekable iterator or generator?

* abstract parent class FilesystemObject
    * extend to file
    * extend to directory


* File
    * test isEmpty


* download documentation package, brc command to edit?
    * make note of string usage '' vs "", latter should only be used when
      required by syntax (and instead of .)
