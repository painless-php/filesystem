<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\File\File;
use Nonetallt\File\Exception\FileNotFoundException;
use Nonetallt\File\Exception\TargetNotFileException;
use Nonetallt\File\Exception\PermissionException;
use Nonetallt\File\Concern\TestsFiles;

class FileTest extends TestCase
{
    use TestsFiles;

	private $file;

    public function setUp() : void
    {
        parent::setUp();
        $this->file = new File(__FILE__);
    }

    public function testExistsReturnsTrueWhenFileExists()
    {
        $this->assertTrue($this->file->exists());
    }

    public function testExistsReturnsFalseWhenFileDoesNotExist()
    {
        $file = new File(__FILE__ . 'foobar');
        $this->assertFalse($file->exists());
    }

    public function testHasExtensionReturnsTrueWhenFileHasExtension()
    {
        $this->assertTrue($this->file->hasExtension());
    }

    public function testHasExtensionReturnsFalseWhenFileHasNoExtension()
    {
        $file = new File('foobar');
        $this->assertFalse($file->hasExtension());
    }

    public function testHasExtensionReturnsTrueWhenFileHasTheSpecifiedExtension()
    {
        $this->assertTrue($this->file->hasExtension('php'));
    }

    public function testHasExtensionReturnsFalseWhenFileDoesNotHaveTheSepcifiedExtension()
    {
        $this->assertFalse($this->file->hasExtension('json'));
    }

    public function testExtensionComparisonWorksWithLeadingDot()
    {
        $this->assertTrue($this->file->hasExtension('.php'));
    }

    public function testGetExtensionReturnsCorrectExtension()
    {
        $this->assertEquals('php', $this->file->getExtension());
    }

    public function testOpenStreamOpensResourceHandleWhenFileExists()
    {
        $this->assertTrue(is_resource($this->file->openStream('r')));
    }

    public function testOpenStreamThrowsFileNotFoundExceptionWhenFileDoesNotExist()
    {
        $this->expectException(FileNotFoundException::class);
        $file = new File('foobar');
        $file->openStream('r');
    }

    public function testOpenStreamThrowsFileNotFoundExceptionWhenFileIsDir()
    {
        $this->expectException(FileNotFoundException::class);
        $file = new File(__DIR__);
        $file->openStream('r');
    }

    public function testOpenStreamThrowsPermissionExceptionWhenReaderHasNoAccess()
    {
        $this->expectException(PermissionException::class);
        $file = new File('/etc/sudoers');
        $file->openStream('r');
    }

    public function testIsFileReturnsTrueWhenPathPointsToFile()
    {
        $this->assertTrue($this->file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathPointsToDir()
    {
        $file = new File(__DIR__);
        $this->assertFalse($file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathDoesNotExist()
    {
        $file = new File(__DIR__ . 'foobar.json');
        $this->assertFalse($file->isFile());
    }

    public function testIsDirReturnsTrueWhenPathPointsToDir()
    {
        $file = new File(__DIR__);
        $this->assertTrue($file->isDirectory());
    }

    public function testIsDirReturnsFalseWhenPathPointsToFile()
    {
        $this->assertFalse($this->file->isDirectory());
    }

    public function testIsDirReturnsFalseWhenPathDoesNotExist()
    {
        $file = new File(__DIR__ . 'foobar.json');
        $this->assertFalse($file->isDirectory());
    }

    public function testGetSizeReturnsInteger()
    {
        $this->assertTrue(is_integer($this->file->getSize()));
    }

    public function testGetSizeThrowsFileNotFoundExceptionWhenFileDoesNotExist()
    {
        $file = new File(__DIR__ . 'foobar.json');
        $this->expectException(FileNotFoundException::class);
        $file->getSize();
    }

    public function testFileImplementsIteratorAggregate()
    {
        $this->assertTrue(in_array(\IteratorAggregate::class, class_implements($this->file)));
    }

    public function testGetContentGetsFileContent()
    {
        $input = new File($this->getTestPath('input/test.json'));
        $this->assertEquals("[\"foobar\"]\n", $input->getContent());
    }

    public function testRenameCreatesFileAtNewPath()
    {
        $path = $this->getTestPath('output/file');
        $file = new File($path);
        $file->create();
        $file->rename('renamed_file');

        $this->assertTrue(file_exists($this->getTestPath('output/renamed_file')));
    }

    public function testRenameDeletesFileAtOldPath()
    {
        $path = $this->getTestPath('output/file');
        $file = new File($path);
        $file->create();
        $file->rename('renamed_file');

        $this->assertFalse(file_exists($path));
    }

    public function testIsEmptyReturnsTrueForEmptyFile()
    {
        $file = new File($this->getTestPath('input/empty.txt'));
        $this->assertTrue($file->isEmpty());
    }

    public function testIsEmptyReturnsFalseForFileWithContent()
    {
        $file = new File($this->getTestPath('input/10_lines.txt'));
        $this->assertFalse($file->isEmpty());
    }
}
