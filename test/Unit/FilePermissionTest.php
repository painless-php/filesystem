<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\File\FilePermissions;

class FilePermissionsTest extends TestCase
{
    private $permissions;

    public function setUp() : void
    {
        parent::setUp();
        $this->permissions = new FilePermissions(__FILE__);
    }

    public function testIsReadableReturnsTrueWhenFileIsReadable()
    {
        $this->assertTrue($this->permissions->isReadable());
    }

    public function testIsReadableReturnsFalseWhenFileDoesNotExist()
    {
        $this->assertFalse((new FilePermissions('foobar'))->isReadable());
    }

    public function testIsReadableReturnsFalseWhenUserDoesNotHavePermissionsToReadFile()
    {
        $this->assertFalse((new FilePermissions('/etc/sudoers'))->isReadable());
    }

    public function testIsWritableReturnsTrueWhenFileIsWritable()
    {
        $this->assertTrue($this->permissions->isWritable());
    }

    public function testIsWritableReturnsFalseWhenFileIsNotWritable()
    {
        $this->assertFalse((new FilePermissions('/etc/sudoers'))->isWritable());
    }

    public function testIsWritableReturnsTrueWhenFileDoesNotExistAndDirectoryIsWritable()
    {
        $this->assertTrue((new FilePermissions(__DIR__ . 'foo.json'))->isWritable());
    }

    public function testIsWritableReturnsFalseWhenFileDoesNotExistAndDirectoryIsNotWritable()
    {
        $this->assertFalse((new FilePermissions('/etc/foo.json'))->isWritable());
    }
}
