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

## Usage examples

#### Reading directory contents

```php
// with iterator
$iterator = Directory::createFromPath($path)->getIterator(
    recursive: true,
    config: new DirectoryIteratorConfig(
        resultFilters: [
            fn(FilesystemObject $file) =>  $file->getExtension() === 'php'
        ]
    )
);

// loop over iterator items
foreach($iterator as FileObject $file) {
    var_dump($file);
}

// turn iterator into array
$array = $iterator->toArray();


// with getContents
$files = Directory::createFromPath($path)->getContents(
    recursive: true,
    config: new DirectoryIteratorConfig(
        resultFilters: [
            fn(FilesystemObject $file) =>  $file->getExtension() === 'php'
        ]
    )
);
```

## Notes
- isRoot() will probably not work on windows (as well as functions that rely on it like findUpwards)

#### DirectoryContentIterator structure

- DirectoryContentIterator (FilterIterator)
    - RecursiveIteratorIterator
    - RecursiveFilterIterator
    - RecursiveDirectoryIterator
