<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\RecursiveFilesystemIterator;
use PHPUnit\Framework\TestCase;

/**
 * @covers RecursiveFilesystemIterator
 *
 */
class RecursiveFilesystemIteratorTest extends TestCase
{
    public function testSupplyingInvalidPathThrowsException()
    {
        $this->expectException(\InvalidArgumentException::class);
         new RecursiveFilesystemIterator('foobar');
    }

    public function testIteratorScansAllDirectories()
    {
        $iterator = new RecursiveFilesystemIterator(dirname(__DIR__) . '/input/recursive');
        $result = [];
        $expected = [
            'recursive/dir1',
            'recursive/dir1/file1_in_dir1',
            'recursive/dir2',
            'recursive/dir3',
        ];

        foreach($iterator as $file) {
            $result[] = $file->getRelativePath(dirname($iterator->getPath()));
        }

        sort($result);
        $this->assertEquals($expected, $result);
    }

    public function testIteratorCanSkipDirectories()
    {
        $iterator = new RecursiveFilesystemIterator(dirname(__DIR__) . '/input/recursive');
        $result = [];
        $expected = [
            'recursive/dir2',
            'recursive/dir3',
        ];

        foreach($iterator as $file) {

            $relativePath = $file->getRelativePath(dirname($iterator->getPath()));

            if($relativePath === 'recursive/dir1') {
                $iterator->skipDirectory();
                continue;
            }

            $result[] = $relativePath;
        }

        sort($result);
        $this->assertEquals($expected, $result);
    }
}
