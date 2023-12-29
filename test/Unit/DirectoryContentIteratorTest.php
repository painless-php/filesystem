<?php

namespace Test\Unit;

use Iterator;
use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\DirectoryContentIteratorConfiguration;
use PainlessPHP\Filesystem\DirectoryContentIterator;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class DirectoryContentIteratorTest extends TestCase
{
    use TestPaths;

    private function iteratorContentsMatch(Iterator $iterator, array $expected)
    {
        $files = [];

        foreach($iterator as $file) {
            $files[] = $file->getFilename();
        }

        // Assert that array contents are the same, disregarding keys
        $this->assertEqualsCanonicalizing($expected, $files);
    }

    public function testItIteratesThroughAllDirectoriesAndFilesByDefault()
    {
        $iterator = new DirectoryContentIterator(
            path: $this->levelThreeDirsPath(),
            config: []
        );

        $this->iteratorContentsMatch($iterator, $this->levelThreeDirsContents());
    }

    public function testItFiltersAllContentsMatchedByScanFilters()
    {
        $iterator = new DirectoryContentIterator(
            path: $this->levelThreeDirsPath(),
            config: new DirectoryContentIteratorConfiguration(
                scanFilters: [
                    function(FilesystemObject $file) {
                        return $file->getFilename() !== '2';
                    }
                ]
            )
        );

        $this->iteratorContentsMatch($iterator, [
            'file_in_base_dir.txt',
            '1',
            'file_in_dir_1.txt'
        ]);
    }

    public function testItFiltersAllContentsMatchedByContentFilters()
    {
        $iterator = new DirectoryContentIterator(
            path: $this->levelThreeDirsPath(),
            config: [
                'contentFilters' => [
                    function(FilesystemObject $file) {
                        return $file->isFile();
                    }
                ]
            ]
        );

        $this->iteratorContentsMatch($iterator, [
            'file_in_base_dir.txt',
            'file_in_dir_1.txt',
            'file_in_dir_2.txt',
            'file_in_dir_3.txt',
        ]);
    }
}
