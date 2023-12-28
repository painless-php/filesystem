<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\DirectoryContentIterator;
use PainlessPHP\Filesystem\DirectoryContentIteratorConfiguration;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class DirectoryContentIteratorTest extends TestCase
{
    use TestPaths;

    public function testIteratorIteratesThroughAllFilesAndDirectories()
    {
        $iterator = new DirectoryContentIterator($this->levelThreeDirsPath());
        $files = [];

        foreach($iterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertContains('1', $files);
        $this->assertContains('2', $files);
        $this->assertContains('3', $files);
        $this->assertContains('file_in_base_dir.txt', $files);
        $this->assertContains('file_in_dir_1.txt', $files);
        $this->assertContains('file_in_dir_2.txt', $files);
        $this->assertContains('file_in_dir_3.txt', $files);
    }

    public function testIteratorSkipsThroughFilteredScansFilesystemObjects()
    {
        $config = new DirectoryContentIteratorConfiguration(
            scanFilters: [
                function(FilesystemObject $file) {
                    return $file->isFile();
                }
            ]
        );

        $iterator = new DirectoryContentIterator(
            path: $this->levelThreeDirsPath(),
            config: $config)
        ;

        $files = [];

        foreach($iterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertContains('file_in_base_dir.txt', $files);
    }

    public function testIteratorSkipsThroughFilteredItemsFilesystemObjects()
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

        $files = [];

        foreach($iterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertContains('file_in_base_dir.txt', $files);
        $this->assertContains('file_in_dir_1.txt', $files);
        $this->assertContains('file_in_dir_2.txt', $files);
        $this->assertContains('file_in_dir_3.txt', $files);
    }
}
