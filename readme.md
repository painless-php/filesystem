# filesystem

Filesystem helper functionality for PHP.

## Installation

```
composer require painless-php/filesystem
```

## Public API

#### Core

* PainlessPHP\Filesystem\Filesystem
* PainlessPHP\Filesystem\FilesystemObject
    * PainlessPHP\Filesystem\File
    * PainlessPHP\Filesystem\Directory
* PainlessPHP\Filesystem\FilesystemPermissions

#### Testing

* PainlessPHP\Filesystem\Concern\DeletesTestOutput

#### Exception

* PainlessPHP\Filesystem\Exception\FilesystemException
    * PainlessPHP\Filesystem\Exception\FileNotFoundException
    * PainlessPHP\Filesystem\Exception\FilesystemPermissionException

## Development

Note that this package uses the following env variables for testing:
* REAL_HOME

## TODO

* Make sure that proper errors are thrown when trying to operate on a non-existent file

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

Filesystem::findUpwards() // support providing filepath and then dirname()
Filesystem::findDownwards() // ^
