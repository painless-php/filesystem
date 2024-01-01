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
* ConfigurationFactory

#### Exception

* FilesystemException
    * FileNotFoundException
    * FilesystemPermissionException

#### Interface

* FilesystemFilter
* DirectoryContentIterator

## TODO

* Inertnalize iterators, only expose interface (and config)

* Configuration
    * option to map top level returned values
    ' ConfigurationFactory
        * $config = ConfigurationFactory::returnFilenames()->create();
    * Rename Configuration class? (DirectoryIterator, DirectoryIteratorConfig)
    * Fix DirectoryTests
    * Use ::CHILD_FIRST?


* Use iterator for Directory recursive deleteContents
* Use iterator for Directory recursive copy

* FilesystemObject::getRelativePath() should work with both children and parent paths
* FilesystemObject::getAbsolutePath() needs a rewrite

* FileLineIterator
    * Support filters with StringFilterInterface
    * writeContent() is really inefficient. Support in-memory modification?
        * WriterInterface


* ComposerJson::locate() (using Filesystem::findUpwards)
* Env editable class

* Make sure that proper errors are thrown when trying to operate on a non-existent file
* Make sure that permission errors throw correct exceptions
* use file's real path for exceptions?

* Directory::delete allow exclusion by relative path and absolute path

## Notes
- isRoot() will probably not work on windows (as well as functions that rely on it like findUpwards)

#### DirectoryContentIterator structure

- DirectoryContentIterator (FilterIterator)
    - RecursiveIteratorIterator
    - RecursiveFilterIterator
    - RecursiveDirectoryIterator
