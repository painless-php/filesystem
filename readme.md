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

#### Contract

* FilesystemFilter

## TODO

* FilesystemObject::getRelativePath() should work with both children and parent paths
* FilesystemObject::getAbsolutePath() needs a rewrite

* FileLine::writeContent() is really inefficient. Support in-memory modification?

* RecursiveFilesystemIterator
    * fix skipDirectory (support levels?)

* Use iterator for Directory recursive deleteContents
* Use iterator for Directory recursive copy

* ComposerJson class
* Env editable class

* Make sure that proper errors are thrown when trying to operate on a non-existent file
* Make sure that permission errors throw correct exceptions
* use file's real path for exceptions?

* Directory::delete allow exclusion by relative path and absolute path

## Notes
- isRoot() will probably not work on windows (as well as functions that rely on it like findUpwards)
