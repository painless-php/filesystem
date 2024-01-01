<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\DirectoryIterator;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class DirectoryIteratorTest extends TestCase
{
    use TestPaths;

    public function testItReturnsAllContentsOnFirstLevel()
    {
        $iterator = new DirectoryIterator(
            path: $this->levelThreeDirsPath(),
            config: []
        );

        $this->assertIterableMatchesContent(
            iterable: $iterator,
            expected: ['file_in_base_dir.txt', '1'],
            mapping: 'filename'
        );
    }

    public function testItCanFilterContents()
    {
        $iterator = new DirectoryIterator(
            path: $this->levelThreeDirsPath(),
            config: new DirectoryIteratorConfig(
                resultFilters: [
                    fn($file) =>  $file->getFilename() === '1'
                ]
            )
        );

        $this->assertIterableMatchesContent(
            iterable: $iterator,
            expected: ['1'],
            mapping: 'filename'
        );
    }
}
