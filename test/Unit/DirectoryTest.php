<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Filesystem\Concern\DeletesTestOutput;
use Nonetallt\Filesystem\Directory;
use Nonetallt\Filesystem\Exception\FilesystemException;
use Nonetallt\Filesystem\Filesystem;

class DirectoryTest extends TestCase
{
    use DeletesTestOutput;

    public function testCreateCreatesDirectory()
    {
        $path = Filesystem::testDirectoryPath('output/directory');
        $dir = new Directory($path);
        $dir->create();

        $this->assertTrue(file_exists($path));
    }

    public function testCreateCreatesDirectoriesRecursively()
    {
        $path = Filesystem::testDirectoryPath('output/foo/bar/baz');
        $dir = new Directory($path);
        $dir->create(true);

        $this->assertTrue(file_exists($path));
    }

    public function testCreateThrowsExceptionIfRecurseIsNotEnabled()
    {
        $path = Filesystem::testDirectoryPath('output/foo/bar/baz');
        $dir = new Directory($path);

        $this->expectException(FilesystemException::class);
        $dir->create();
    }

    public function testExistsReturnsTrueWhenDirectoryExists()
    {
        $dir = new Directory(Filesystem::testDirectoryPath('input/directory'));
        $this->assertTrue($dir->exists());
    }

    public function testExistsReturnsFalseWhenDirectoryDoesNotExists()
    {
        $dir = new Directory(Filesystem::testDirectoryPath('input/foo'));
        $this->assertFalse($dir->exists());
    }

    public function testMoveRenamesDirectory()
    {
        $oldPath = Filesystem::testDirectoryPath('output/old/1');
        $dir = new Directory($oldPath);
        $dir->create(true);

        $newPath = Filesystem::testDirectoryPath('output/new/1');
        $dir->move($newPath, true);

        $this->assertFalse(file_exists($oldPath));
        $this->assertTrue(file_exists($newPath));
    }

    public function testMoveMovesDirectoryContents()
    {
        $oldPath = Filesystem::testDirectoryPath('output/foo');
        $dir = new Directory($oldPath);
        $dir->create();
        file_put_contents("$oldPath/file", 'content');

        $newPath = Filesystem::testDirectoryPath('output/bar');
        $dir->move($newPath);

        $this->assertFalse(file_exists("$oldPath/file"));
        $this->assertTrue(file_exists("$newPath/file"));
    }

    public function testGetSizeRecursiveReturnsCombinedSizeOfDirectoryAndItsContents()
    {
        $dir = new Directory(Filesystem::testDirectoryPath('input/size'));
        $this->assertEquals(9280, $dir->getSize());
    }

    public function testGetPathReturnsPathname()
    {
        $path = Filesystem::testDirectoryPath('input/size');
        $dir = new Directory($path);
        $this->assertEquals($path, $dir->getPath());
    }

    public function testDeleteContentsThrowsExceptionWhenTryingToDeleteNonEmptyDirWithoutRecursive()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $this->expectException(FilesystemException::class);
        $dir->delete();
    }

    public function testDeleteContentsDoesNotDeleteDirectoryItself()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->deleteContents();

        $this->assertTrue(file_exists($path));
    }

    public function testDeleteContentsDeletesDirectoryContents()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->deleteContents();

        $this->assertFalse(file_exists("$path/file"));
    }

    public function testDeleteDeletesDirectory()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        $dir->delete();

        $this->assertFalse(file_exists($path));
    }

    public function testRecursiveDeleteDeletesDirectoryContents()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->delete(true);
        $this->assertFalse(file_exists("$path/file"));
    }

    public function testIsEmptyReturnsTrueWhenDirIsEmpty()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();

        $this->assertTrue($dir->isEmpty());
    }

    public function testIsEmptyReturnsFalseWhenDirHasFiles()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $this->assertFalse($dir->isEmpty());
    }

    public function testIsEmptyReturnsFalseWhenDirHasDirectories()
    {
        $path = Filesystem::testDirectoryPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        mkdir("$path/dir");

        $this->assertFalse($dir->isEmpty());
    }

    public function testGetNameReturnsNameOfTheDirectory()
    {
        $dir = new Directory('/foo/bar/baz/dir');
        $this->assertEquals('dir', $dir->getName());
    }
}
