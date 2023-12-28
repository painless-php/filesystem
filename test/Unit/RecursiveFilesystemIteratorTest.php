<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\RecursiveFilesystemIterator;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class RecursiveFilesystemIteratorTest extends TestCase
{
    use TestPaths;

    public function testIteratorIteratesThroughAllFilesAndDirectories()
    {
        $iterator = new RecursiveFilesystemIterator($this->levelThreeDirsPath());
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
        $iterator = new RecursiveFilesystemIterator(path: $this->levelThreeDirsPath(), scanFilters: [function(FilesystemObject $file) {
            return $file->isFile();
        }]);

        $files = [];

        foreach($iterator as $file) {
            $files[] = $file->getFilename();
        }

        $this->assertContains('file_in_base_dir.txt', $files);
    }

    public function testIteratorSkipsThroughFilteredItemsFilesystemObjects()
    {
        $iterator = new RecursiveFilesystemIterator(path: $this->levelThreeDirsPath(), itemFilters: [function(FilesystemObject $file) {
            return $file->isFile();
        }]);

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
