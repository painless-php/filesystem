<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Filesystem\File;
use Nonetallt\Filesystem\Exception\FileNotFoundException;
use Nonetallt\Filesystem\Exception\TargetNotFileException;
use Nonetallt\Filesystem\Exception\PermissionException;
use Nonetallt\Filesystem\Concern\TestsFiles;

class FileModificationTest extends TestCase
{
    use TestsFiles;

    private $file;

    public function setUp() : void
    {
        parent::setUp();
        $this->file = new File($this->getTestPath('input/10_lines.txt'));
    }

    private function copyToOutput(string $file) : File
    {
        $source = new File($this->getTestPath("input/$file"));
        $outputPath = $this->getTestPath("output/$file");
        $source->copy($outputPath);

        return new File($outputPath);
    }

    public function testCopyCreatesFileWithCopiedContent()
    {
        $outputPath = $this->getTestPath('output/10_lines.txt');
        $this->file->copy($outputPath);
        $output = new File($outputPath);
        $this->assertEquals($this->file->getContent(), $output->getContent());
    }

    public function testMoveRemovesOldFile()
    {
        $file = $this->copyToOutput('10_lines.txt');
        $oldPath = $file->getPathname();
        $file->move($this->getTestPath('output/new.txt'));

        $this->assertFalse(file_exists($oldPath));
    }

    public function testMoveCreatesNewFile()
    {
        $file = $this->copyToOutput('10_lines.txt');
        $newPath = $this->getTestPath('output/new.txt');
        $file->move($newPath);

        $this->assertTrue(file_exists($newPath));
    }

    public function testRenameRemovesOldFile()
    {
        $file = $this->copyToOutput('10_lines.txt');
        $oldPath = $file->getPathname();
        $file->rename('new.txt');

        $this->assertFalse(file_exists($oldPath));
    }

    public function testRenameCreatesNewFile()
    {
        $file = $this->copyToOutput('10_lines.txt');
        $file->rename('new.txt');
        $this->assertTrue(file_exists($this->file->getPathname()));
    }

    public function testWriteString()
    {
        $file = $this->copyToOutput('10_lines.txt');
        $file->write('foo');

        $this->assertEquals('foo', $file->getContent());
    }

    public function testWriteLines()
    {
        $file = $this->copyToOutput('existing_file.txt');
        $file->write($this->file->getLines());

        $this->assertEquals($file->getContent(), $this->file->getContent());
    }
}
