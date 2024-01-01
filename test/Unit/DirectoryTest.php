<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Filesystem;
use PainlessPHP\Filesystem\FilesystemObject;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class DirectoryTest extends TestCase
{
    use TestPaths;

    public function tearDown() : void
    {
        parent::tearDown();
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
        $this->assertDirectoryDoesNotExist($path);

        $directory = new Directory($path);
        $directory->create(recursive: false);

        $this->assertDirectoryExists($path);
    }

    public function testCreateCreatesNestedDirectoriesOnFilesystemWhenRecursive()
    {
        $path = $this->getOutputPath('test-dir-1', 'test-dir-2');
        $this->assertDirectoryDoesNotExist(dirname($path));
        $this->assertDirectoryDoesNotExist($path);

        $directory = new Directory($path);
        $directory->create(recursive: true);

        $this->assertDirectoryExists(dirname($path));
        $this->assertDirectoryExists($path);
    }

    public function testCopyCreatesTheDirectoryAtDestination()
    {
        $outputPath = $this->getOutputPath('test-dir');
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $directory->copy(destination: $outputPath, recursive: false);
        Directory::createFromPath($outputPath);

        $this->assertDirectoryExists($outputPath);
    }

    public function testCopyCopiesAllNestedFilesAndDirectoriesWhenRecursive()
    {
        $outputPath = $this->getOutputPath('test-dir');
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $directory->copy(destination: $outputPath, recursive: true);
        $outputDir = Directory::createFromPath($outputPath);

        $this->assertIterableMatchesContent(
            iterable: $outputDir->getContents(recursive: true),
            mapping: 'filename',
            expected: $this->levelThreeDirsContents()
        );
    }

    public function testCopyCopiesOnlyTheFilesThatPassFilters()
    {
        $outputPath = $this->getOutputPath('test-dir');
        $directory = Directory::createFromPath($this->levelThreeDirsPath());
        $filesToCopy = ['1', '2', '3'];

        $directory->copy(destination: $outputPath, recursive: true, config: new DirectoryIteratorConfig(resultFilters: [
            fn($file) => in_array($file->getFilename(), $filesToCopy)
        ]));
        $outputDir = Directory::createFromPath($outputPath);

        $this->assertIterableMatchesContent(
            iterable: $outputDir->getContents(recursive: true),
            mapping: 'filename',
            expected: $filesToCopy
        );
    }

    public function testDeleteDeletesTheDirectory()
    {
        $targetPath = $this->getOutputPath('test-dir');
        $this->assertDirectoryDoesNotExist($targetPath);

        mkdir($targetPath);
        $this->assertDirectoryExists($targetPath);
        $directory = Directory::createFromPath($targetPath);

        $this->assertTrue($directory->delete(recursive: false));
        $this->assertDirectoryDoesNotExist($targetPath);
    }

    public function testDeleteDoesNotDeleteDirectoryWhenDirectoryHasContents()
    {
        $targetPath = $this->getOutputPath('test-dir');
        $this->assertDirectoryDoesNotExist($targetPath);

        mkdir($targetPath);
        file_put_contents(Filesystem::appendToPath($targetPath, 'foo.txt'), 'content');
        $this->assertDirectoryExists($targetPath);
        $directory = Directory::createFromPath($targetPath);

        $this->assertFalse($directory->delete(recursive: false));
        $this->assertDirectoryExists($targetPath);
    }

    public function testDeleteDeletesTheDirectoryAndItsContentsWhenRecursive()
    {
        $targetPath = $this->getOutputPath('test-dir');
        $this->assertDirectoryDoesNotExist($targetPath);

        mkdir($targetPath);
        file_put_contents(Filesystem::appendToPath($targetPath, 'foo.txt'), 'content');
        $this->assertDirectoryExists($targetPath);
        $directory = Directory::createFromPath($targetPath);

        $this->assertTrue($directory->delete(recursive: true));
        $this->assertDirectoryDoesNotExist($targetPath);
    }

    public function testNonRecursiveIteratorIsArrayable()
    {
        $contents = Directory::createFromPath($this->levelThreeDirsPath())->getIterator(recursive: false)->toArray();
        $this->assertIterableMatchesContent(['1', 'file_in_base_dir.txt'], $contents, 'filename');
    }

    public function testRecursiveIteratorIsArrayable()
    {
        $contents = Directory::createFromPath($this->levelThreeDirsPath())->getIterator(recursive: true)->toArray();
        $this->assertIterableMatchesContent($this->levelThreeDirsContents(), $contents, 'filename');
    }
}
