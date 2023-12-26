<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\Filter\FileFilesystemFilter;
use PHPUnit\Framework\TestCase;
use Test\Concern\TestPaths;

class DirectoryTest extends TestCase
{
    use TestPaths;

    public function testGetContentsReturnsFilesystemObjectsInDirectory()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $expected = [
            '1',
            'file_in_base_dir.txt',
        ];

        $this->assertSame($expected, array_map(fn($file) => $file->getFilename(), $directory->getContents()));
    }

    public function testGetContentsReturnsAllContentsWhenRecursiveIsTrue()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $expected = [
            '1',
            '2',
            '3',
            'file_in_base_dir.txt',
            'file_in_dir_1.txt',
            'file_in_dir_2.txt',
            'file_in_dir_3.txt'
        ];

        $contents = $directory->getContents(recursive: true);
        $contents = array_map(fn($file) => $file->getFilename(), $contents);
        sort($contents);

        $this->assertSame($expected, $contents);
    }

    public function testGetCotentsFiltersContentBasedOnIteratorSettings()
    {
        $directory = Directory::createFromPath($this->levelThreeDirsPath());

        $expected = [
            'file_in_base_dir.txt',
            'file_in_dir_1.txt',
            'file_in_dir_2.txt',
            'file_in_dir_3.txt'
        ];

        $contents = $directory->getContents(
            recursive: true,
            iteratorArguments: [
                'itemFilters' => [
                    new FileFilesystemFilter
                ]
            ]
        );

        $contents = array_map(fn($file) => $file->getFilename(), $contents);
        sort($contents);

        $this->assertSame($expected, $contents);
    }
}
