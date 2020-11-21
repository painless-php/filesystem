<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Filesystem\File;
use Nonetallt\Filesystem\Concern\TestsFiles;

class FileLineTest extends TestCase
{
    use TestsFiles; 

    private $file;

    public function setUp() : void
    {
        parent::setUp();

        $this->file = new File($this->getTestPath('input/10_lines.txt'));
        $this->file->copy($this->getTestPath('output/10_lines.txt'));
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
