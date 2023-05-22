<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use PainlessPHP\Filesystem\File;
use PainlessPHP\Filesystem\Concern\DeletesTestOutput;
use PainlessPHP\Filesystem\Filesystem;

class FileLineTest extends TestCase
{
    use DeletesTestOutput;

    private $file;

    public function setUp() : void
    {
        parent::setUp();

        $this->file = new File(Filesystem::testDirectoryPath('input/10_lines.txt'));
        $this->file->copy(Filesystem::testDirectoryPath('output/10_lines.txt'));
    }

    public function test()
    {
        foreach($this->file as $index => $line) {
            /* dd('asd'); */
            /* dd($line->writeContent('foo')); */
            /* dd($line->getNumber()); */
            /* echo ($line); */
        }
    }
}
