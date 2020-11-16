# file

PHP file manipulation using OOP.

## Public API

#### Core

* Nonetallt\File\File
* Nonetallt\File\FilePermissions

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

* File->create(true) - create path recursively
* File->delete(true) - delete contents recursively if directory
