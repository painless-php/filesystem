<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\RecursiveFilesystemIterator;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

/**
 * @covers RecursiveFilesystemIterator
 *
 */
class RecursiveFilesystemIteratorTest extends TestCase
{
    public function testSupplyingInvalidPathThrowsException()
    {
        $this->expectException(InvalidArgumentException::class);
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
            'recursive/dir2/file2_in_dir2',
            'recursive/dir3',
            'recursive/dir3/file3_in_dir3',
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
            'recursive/dir2/file2_in_dir2',
            'recursive/dir3',
            'recursive/dir3/file3_in_dir3',

        ];

        foreach($iterator as $file) {

            $relativePath = $file->getRelativePath(dirname($iterator->getPath()));
            var_dump($relativePath);

            if($relativePath === 'recursive/dir1') {
                var_dump('SKIP');
                $iterator->skipDirectory();
                continue;
            }

            $result[] = $relativePath;
        }

        sort($result);
        $this->assertEquals($expected, $result);
    }
}
