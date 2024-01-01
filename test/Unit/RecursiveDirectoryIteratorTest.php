<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\RecursiveDirectoryIterator;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class RecursiveDirectoryIteratorTest extends TestCase
{
    use TestPaths;

    public function testItIteratesThroughAllDirectoriesAndFilesByDefault()
    {
        $iterator = new RecursiveDirectoryIterator(
            path: $this->levelThreeDirsPath(),
            config: []
        );

        $this->assertIterableMatchesContent(
            iterable: $iterator,
            expected: $this->levelThreeDirsContents(),
            mapping: 'filename'
        );
    }

    public function testItFiltersAllContentsMatchedByReadFilters()
    {
        $iterator = new RecursiveDirectoryIterator(
            path: $this->levelThreeDirsPath(),
            config: new DirectoryIteratorConfig(
                recursionFilters: [
                    function(FilesystemObject $file) {
                        return $file->getFilename() !== '2';
                    }
                ]
            )
        );

        $this->assertIterableMatchesContent(
            iterable: $iterator,
            mapping: 'filename',
            expected: [
                'file_in_base_dir.txt',
                '1',
                'file_in_dir_1.txt'
            ]
        );
    }

    public function testItFiltersAllContentsMatchedByResultFilters()
    {
        $iterator = new RecursiveDirectoryIterator(
            path: $this->levelThreeDirsPath(),
            config: new DirectoryIteratorConfig(
                resultFilters: [
                    function(FilesystemObject $file) {
                        return $file->isFile();
                    }
                ])
        );

        $this->assertIterableMatchesContent(
            iterable: $iterator,
            mapping: 'filename',
            expected: [
                'file_in_base_dir.txt',
                'file_in_dir_1.txt',
                'file_in_dir_2.txt',
                'file_in_dir_3.txt',
            ]
        );
    }
}
