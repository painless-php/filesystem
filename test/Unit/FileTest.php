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
        $this->file->setPathname(__FILE__ . 'foobar');
        $this->assertFalse($this->file->exists());
    }

    public function testHasExtensionReturnsTrueWhenFileHasExtension()
    {
        $this->assertTrue($this->file->hasExtension());
    }

    public function testHasExtensionReturnsFalseWhenFileHasNoExtension()
    {
        $this->file->setPathname('foobar');
        $this->assertFalse($this->file->hasExtension());
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
        $this->file->setPathname('foobar');
        $this->file->openStream('r');
    }

    public function testOpenStreamThrowsTargetNotFileExceptionWhenFileIsDir()
    {
        $this->expectException(TargetNotFileException::class);
        $this->file->setPathname(__DIR__);
        $this->file->openStream('r');
    }

    public function testOpenStreamThrowsPermissionExceptionWhenReaderHasNoAccess()
    {
        $this->expectException(PermissionException::class);
        $this->file->setPathname('/etc/sudoers');
        $this->file->openStream('r');
    }

    public function testIsFileReturnsTrueWhenPathPointsToFile()
    {
        $this->assertTrue($this->file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathPointsToDir()
    {
        $this->file->setPathname(__DIR__);
        $this->assertFalse($this->file->isFile());
    }

    public function testIsFileReturnsFalseWhenPathDoesNotExist()
    {
        $this->file->setPathname(__DIR__ . 'foobar.json');
        $this->assertFalse($this->file->isFile());
    }

    public function testIsDirReturnsTrueWhenPathPointsToDir()
    {
        $this->file->setPathname(__DIR__);
        $this->assertTrue($this->file->isDir());
    }

    public function testIsDirReturnsFalseWhenPathPointsToFile()
    {
        $this->assertFalse($this->file->isDir());
    }

    public function testIsDirReturnsFalseWhenPathDoesNotExist()
    {
        $this->file->setPathname(__DIR__ . 'foobar.json');
        $this->assertFalse($this->file->isDir());
    }

    public function testGetSizeReturnsInteger()
    {
        $this->assertTrue(is_integer($this->file->getSize()));
    }

    public function testGetSizeThrowsFileNotFoundExceptionWhenFileDoesNotExist()
    {
        $this->file->setPathname(__DIR__ . 'foobar.json');
        $this->expectException(FileNotFoundException::class);
        $this->file->getSize();
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
}
