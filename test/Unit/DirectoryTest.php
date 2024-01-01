<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\FilesystemObject;
use PainlessPHP\Filesystem\Filter\FileFilesystemFilter;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class DirectoryTest extends TestCase
{
    use TestPaths;

    public function tearDown() : void
    {
        parent::tearDown();
        var_dump('CLEAN OUTPUT');
        $this->cleanOutput();
    }

    public function testGetContentsReturnsFilesystemObjectsInDirectory()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $this->assertIterableMatchesContent(
            iterable: $directory->getContents(),
            mapping: 'filename',
            expected: [
                '1',
                'file_in_base_dir.txt',
            ]
        );
    }

    public function testGetContentsReturnsAllContentsWhenRecursiveIsTrue()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $this->assertIterableMatchesContent(
            iterable: $directory->getContents(recursive: true),
            mapping: 'filename',
            expected: $this->levelThreeDirsContents()
        );
    }

    public function testGetContentsFiltersResultBasedOnIteratorSettings()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $this->assertIterableMatchesContent(
            iterable: $directory->getContents(
                recursive: true,
                config: ['resultFilters' => [
                    fn(FilesystemObject $file) => $file->isFile()
                ]]
            ),
            mapping: 'filename',
            expected: [
                'file_in_base_dir.txt',
                'file_in_dir_1.txt',
                'file_in_dir_2.txt',
                'file_in_dir_3.txt'
            ]
        );
    }

    public function testCreateCreatesTheDirectoryOnFilesystem()
    {
        $path = $this->getOutputPath('test-dir');
        $this->assertFileDoesNotExist($path);

        $directory = new Directory($path);
        $directory->create(recursive: false);

        $this->assertTrue(is_dir($path));
    }

    public function testCreateCreatesNestedDirectoriesOnFilesystemWhenRecursive()
    {
        $path = $this->getOutputPath('test-dir-1', 'test-dir-2');
        $this->assertFileDoesNotExist(dirname($path));
        $this->assertFileDoesNotExist($path);

        $directory = new Directory($path);
        $directory->create(recursive: true);

        $this->assertTrue(is_dir(dirname($path)));
        $this->assertTrue(is_dir($path));
    }

    public function testCopyCopiesFirstLevelFilesAndDirectories()
    {
        $outputPath = $this->getOutputPath();
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $directory->copy(destination: $outputPath, recursive: false);
        $outputDir = Directory::createFromPath($outputPath);

        $this->assertIterableMatchesContent(
            iterable: $outputDir->getContents(recursive: true),
            mapping: 'filename',
            expected: $this->levelThreeDirsContents()
        );
    }

    public function testCopyCopiesAllNestedFilesAndDirectoriesWhenRecursive()
    {
        $outputPath = $this->getOutputPath();
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $directory->copy($outputPath);
        $outputDir = Directory::createFromPath($outputPath);

        $this->assertIterableMatchesContent(
            iterable: $outputDir->getContents(recursive: true),
            mapping: 'filename',
            expected: $this->levelThreeDirsContents()
        );
    }
}
