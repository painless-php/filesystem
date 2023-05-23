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
FilesystemObject::getRelativePath() should work with both children and parent paths

RecursiveFilesystemIterator
* support exclusion of files (support patterns?)
* fix skipDirectory (support levels?)

Use iterator for Directory recursive deleteContents
Use iterator for Directory recursive copy

ComposerJson class
Env editable class

* Make sure that proper errors are thrown when trying to operate on a non-existent file
* Make sure that permission errors throw correct exceptions
* use file's real path for exceptions?

* Directory::delete allow exclusion by relative path and absolute path
