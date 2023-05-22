<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use PainlessPHP\Filesystem\File;
use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Exception\TargetNotFileException;
use PainlessPHP\Filesystem\Exception\PermissionException;
use PainlessPHP\Filesystem\Concern\DeletesTestOutput;
use PainlessPHP\Filesystem\Filesystem;

class FileTest extends TestCase
{
    use DeletesTestOutput;

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

    public function testGetExtensionWorksForDotfiles()
    {
        $file = new File('.env');
        $this->assertNull($file->getExtension());
    }

    public function testGetExtensionWorksWithFilesThatHaveMultipleExtensions()
    {
        $file = new File('index.blade.php');
        $this->assertEquals('blade.php', $file->getExtension());
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
        $input = new File(Filesystem::testDirectoryPath('input/test.json'));
        $this->assertEquals("[\"foobar\"]\n", $input->getContent());
    }

    public function testRenameCreatesFileAtNewPath()
    {
        $path = Filesystem::testDirectoryPath('output/file');
        $file = new File($path);
        $file->create();
        $file->rename('renamed_file');

        $this->assertTrue(file_exists(Filesystem::testDirectoryPath('output/renamed_file')));
    }

    public function testRenameDeletesFileAtOldPath()
    {
        $path = Filesystem::testDirectoryPath('output/file');
        $file = new File($path);
        $file->create();
        $file->rename('renamed_file');

        $this->assertFalse(file_exists($path));
    }

    public function testIsEmptyReturnsTrueForEmptyFile()
    {
        $file = new File(Filesystem::testDirectoryPath('input/empty.txt'));
        $this->assertTrue($file->isEmpty());
    }

    public function testIsEmptyReturnsFalseForFileWithContent()
    {
        $file = new File(Filesystem::testDirectoryPath('input/10_lines.txt'));
        $this->assertFalse($file->isEmpty());
    }

    public function testGetNameReturnsFilenameWithExtension()
    {
        $file = new File('/foo/bar/home.blade.php');
        $this->assertEquals('home.blade.php', $file->getName());
    }

    public function testGetBaseNameReturnsFilenameWithoutExtension()
    {
        $file = new File('/foo/bar/home.blade.php');
        $this->assertEquals('home', $file->getBaseName());
    }

    public function testGetRelativePathReturnsRelativePath()
    {
        $file = new File('/foo/bar/baz/file');
        $this->assertEquals('baz/file', $file->getRelativePath('/foo/bar'));
    }

    public function testGetAbsolutePathConvertsLeadingTildeToHome()
    {
        $file = new File('~');
        $this->assertEquals($_ENV['REAL_HOME'], $file->getAbsolutePath());
    }

    public function testGetAbsolutePathConvertsLeadingTildeWithPath()
    {
        $file = new File('~/foo/bar');
        $expected = $_ENV['REAL_HOME'] . '/foo/bar';
        $this->assertEquals($expected, $file->getAbsolutePath());
    }
}
