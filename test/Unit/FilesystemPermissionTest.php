<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use Nonetallt\Filesystem\FilesystemPermissions;

class FilesystemPermissionsTest extends TestCase
{
    private $permissions;

    public function setUp() : void
    {
        parent::setUp();
        $this->permissions = new FilesystemPermissions(__FILE__);
    }

    public function testIsReadableReturnsTrueWhenFileIsReadable()
    {
        $this->assertTrue($this->permissions->isReadable());
    }

    public function testIsReadableReturnsFalseWhenFileDoesNotExist()
    {
        $this->assertFalse((new FilesystemPermissions('foobar'))->isReadable());
    }

    public function testIsReadableReturnsFalseWhenUserDoesNotHavePermissionsToReadFile()
    {
        $this->assertFalse((new FilesystemPermissions('/etc/sudoers'))->isReadable());
    }

    public function testIsWritableReturnsTrueWhenFileIsWritable()
    {
        $this->assertTrue($this->permissions->isWritable());
    }

    public function testIsWritableReturnsFalseWhenFileIsNotWritable()
    {
        $this->assertFalse((new FilesystemPermissions('/etc/sudoers'))->isWritable());
    }

    public function testIsWritableReturnsTrueWhenFileDoesNotExistAndDirectoryIsWritable()
    {
        $this->assertTrue((new FilesystemPermissions(__DIR__ . 'foo.json'))->isWritable());
    }

    public function testIsWritableReturnsFalseWhenFileDoesNotExistAndDirectoryIsNotWritable()
    {
        $this->assertFalse((new FilesystemPermissions('/etc/foo.json'))->isWritable());
    }
}
