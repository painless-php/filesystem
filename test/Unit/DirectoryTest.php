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
        // $this->cleanOutput();
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

    public function testCopyCopiesFirstLevelFilesAndDirectories()
    {
        $outputPath = $this->getOutputPath();
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $directory->copy($outputPath);

        $outputDir = Directory::createFromPath($outputPath);
        $contents = $outputDir->getContents(recursive: true);
        $contents = array_map(fn($file) => $file->getFilename(), $contents);

        $expected = [
            'file_in_base_dir',
            '1'
        ];

        $this->assertSame($expected, $contents);
    }

    public function testCopyCopiesAllNestedFilesAndDirectoriesWhenRecursive()
    {
        // $outputPath = $this->getOutputPath();
        // $directory = Directory::createFromPath($this->levelThreeDirsPath());
        // $directory->copy($outputPath);

        // $outputDir = Directory::createFromPath($outputPath);
        // $contents = $outputDir->getContents(recursive: true);
        // $contents = array_map(fn($file) => $file->getFilename(), $contents);
        // sort($contents);

        // $expected = [
        //     '1',
        //     '2',
        //     '3',
        //     'file_in_base_dir.txt',
        //     'file_in_dir_1.txt',
        //     'file_in_dir_2.txt',
        //     'file_in_dir_3.txt'
        // ];

        // $this->assertSame($expected, $contents);
    }
}
