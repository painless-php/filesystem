<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Filesystem\Filesystem;

class FilesystemTest extends TestCase
{
    public function testComposerAutoloaderPathReturnsExpectedPath()
    {
        $expected = implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            'vendor',
            'autoload.php'
        ]);

        $this->assertEquals($expected, Filesystem::composerAutoloaderPath());
    }

    public function testProjectDirectoryPathReturnsExpectedPath()
    {
        $this->assertEquals(dirname(__DIR__, 2), Filesystem::projectDirectoryPath());
    }

    public function testTestDirectoryPathReturnsExpectedPath()
    {
        $expected = implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            'test'
        ]);

        $this->assertEquals($expected, Filesystem::testDirectoryPath());
    }

    public function testAppendToPathAppendsWithDirectorySeparator()
    {
        $path = __DIR__;
        $append = 'foo/bar/baz';

        $expected = $path . DIRECTORY_SEPARATOR . $append;
        $this->assertEquals($expected, Filesystem::appendToPath($path, $append));
    }
}
