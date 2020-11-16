<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\File\Concern\TestsFiles;
use Nonetallt\File\Directory;
use Nonetallt\File\Exception\FilesystemException;

class DirectoryTest extends TestCase
{
    use TestsFiles;

    public function testCreateCreatesDirectory()
    {
        $path = $this->getTestPath('output/directory');
        $dir = new Directory($path);
        $dir->create();

        $this->assertTrue(file_exists($path));
    }

    public function testCreateCreatesDirectoriesRecursively()
    {
        $path = $this->getTestPath('output/foo/bar/baz');
        $dir = new Directory($path);
        $dir->create(true);

        $this->assertTrue(file_exists($path));
    }

    public function testCreateThrowsExceptionIfRecurseIsNotEnabled()
    {
        $path = $this->getTestPath('output/foo/bar/baz');
        $dir = new Directory($path);

        $this->expectException(FilesystemException::class);
        $dir->create();
    }

    public function testExistsReturnsTrueWhenDirectoryExists()
    {
        $dir = new Directory($this->getTestPath('input/directory'));
        $this->assertTrue($dir->exists());
    }

    public function testExistsReturnsFalseWhenDirectoryDoesNotExists()
    {
        $dir = new Directory($this->getTestPath('input/foo'));
        $this->assertFalse($dir->exists());
    }

    public function testMoveRenamesDirectory()
    {
        $oldPath = $this->getTestPath('output/old/1');
        $dir = new Directory($oldPath);
        $dir->create(true);

        $newPath = $this->getTestPath('output/new/1');
        $dir->move($newPath, true);

        $this->assertFalse(file_exists($oldPath));
        $this->assertTrue(file_exists($newPath));
    }

    public function testMoveMovesDirectoryContents()
    {
        $oldPath = $this->getTestPath('output/foo');
        $dir = new Directory($oldPath);
        $dir->create();
        file_put_contents("$oldPath/file", 'content');

        $newPath = $this->getTestPath('output/bar');
        $dir->move($newPath);

        $this->assertFalse(file_exists("$oldPath/file"));
        $this->assertTrue(file_exists("$newPath/file"));
    }

    public function testGetSizeReturnsCombinedSizeOfDirectoryContents()
    {
        $dir = new Directory($this->getTestPath('input/size'));
        $this->assertEquals(5184, $dir->getSize());
    }

    public function testGetPathReturnsPathname()
    {
        $path = $this->getTestPath('input/size');
        $dir = new Directory($path);
        $this->assertEquals($path, $dir->getPath());
    }

    public function testDeleteContentsThrowsExceptionWhenTryingToDeleteNonEmptyDirWithoutRecursive()
    {
        $path = $this->getTestPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $this->expectException(FilesystemException::class);
        $dir->delete();
    }

    public function testDeleteContentsDoesNotDeleteDirectoryItself()
    {
        $path = $this->getTestPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->deleteContents();

        $this->assertTrue(file_exists($path));
    }

    public function testDeleteContentsDeletesDirectoryContents()
    {
        $path = $this->getTestPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->deleteContents();

        $this->assertFalse(file_exists("$path/file"));
    }

    public function testDeleteDeletesDirectory()
    {
        $path = $this->getTestPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        $dir->delete();

        $this->assertFalse(file_exists($path));
    }

    public function testRecursiveDeleteDeletesDirectoryContents()
    {
        $path = $this->getTestPath('output/dir');
        $dir = new Directory($path);
        $dir->create();
        file_put_contents("$path/file", 'content');

        $dir->delete(true);
        $this->assertFalse(file_exists("$path/file"));
    }
}
