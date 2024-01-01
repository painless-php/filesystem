<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase
{
    public function testGetExtensionReturnsTheFileExtension()
    {
        $file = new File('foo.bar');
        $this->assertSame('bar', $file->getExtension());
    }
}
