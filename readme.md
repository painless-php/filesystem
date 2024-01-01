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
* DirectoryIteratorConfig

#### Exception

* FilesystemException
    * FileNotFoundException
    * FilesystemPermissionException

#### Interface

* FilesystemFilter
* DirectoryContentIterator

## Notes
- isRoot() will probably not work on windows (as well as functions that rely on it like findUpwards)

#### DirectoryContentIterator structure

- DirectoryContentIterator (FilterIterator)
    - RecursiveIteratorIterator
    - RecursiveFilterIterator
    - RecursiveDirectoryIterator
