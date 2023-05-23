# filesystem

Filesystem helper functionality for PHP.

## Installation

```
composer require painless-php/filesystem
```

## Public API

#### Core

* Filesystem
* FilesystemObject
    * File
    * Directory

#### Exception

* FilesystemException
    * FileNotFoundException
    * FilesystemPermissionException

## TODO

Filesystem::findUpwards() // support providing filepath and then dirname()
Filesystem::findDownwards() // ^
FilesysmteObject::getRelativePath() should work with both children and parent paths

* Make sure that proper errors are thrown when trying to operate on a non-existent file
* Make sure that permission errors throw correct exceptions
* use file's real path for exceptions?

* Directory
    * allow deletion exclusion by relative path and absolute path
