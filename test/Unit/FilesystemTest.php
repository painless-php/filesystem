<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\Exception\FileNotFoundException;
use PainlessPHP\Filesystem\Filesystem;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class FilesystemTest extends TestCase
{
    use TestPaths;

    public function testFindUpwardsCanFindProjectComposerJson()
    {
        $target = 'composer.json';
        $file = Filesystem::findUpwards(startPath: __FILE__, target: $target);
        $this->assertSame($target, $file->getFilename());
    }

    public function testFindUpwardsThrowsExceptionWhenFileIsNotFound()
    {
        $target = '';
        $this->expectException(FileNotFoundException::class);
        Filesystem::findUpwards(startPath: __FILE__, target: $target);
    }

    public function testFindDownwardCanFindProjectComposerJson()
    {
        $target = 'file_in_dir_3.txt';
        $start = $this->levelThreeDirsPath();
        $file = Filesystem::findDownwards(startPath: $start, target: $target);
        $this->assertSame($target, $file->getFilename());
    }

    public function testFindDownwardsThrowsExceptionWhenFileIsNotFound()
    {
        $target = '';
        $this->expectException(FileNotFoundException::class);
        Filesystem::findDownwards(startPath: __FILE__, target: $target);
    }
}
