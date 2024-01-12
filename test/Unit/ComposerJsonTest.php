<?php

namespace Test\Unit;

use PainlessPHP\Filesystem\ComposerJson;
use PainlessPHP\Filesystem\File;
use PHPUnit\Framework\TestCase;
use Test\Trait\TestPaths;

class ComposerJsonTest extends TestCase
{
    use TestPaths;

    private string $composerLocation;

    public function setUp() : void
    {
        $this->composerLocation = $this->getProjectRootPath('composer.json');
    }

    public function testLocateCreatesObjectWithCorrectPath()
    {
        $this->assertSame($this->composerLocation, ComposerJson::locate(__DIR__)->getPathname());
    }

    public function testGetParsedContentHasTopLevelKeysWhenCalledWithoutParameters()
    {
        $composer = new ComposerJson($this->composerLocation);
        $expected = [
            'name',
            'type',
            'description'
        ];

        $parsedKeys = array_keys($composer->getParsedContent());

        foreach($expected as $expectedKey) {
            $this->assertContains($expectedKey, $parsedKeys);
        }
    }

    public function testGetParsedContentCanReturnTopLevelKeyValue()
    {
        $composer = new ComposerJson($this->composerLocation);
        $this->assertSame('painless-php/filesystem', $composer->getParsedContent('name'));
    }

    public function testGetParsedContentCanReturnNestedKeyValue()
    {
        $composer = new ComposerJson($this->composerLocation);
        $expected = ['PainlessPHP\\Filesystem\\' => 'src/'];

        $this->assertSame($expected, $composer->getParsedContent('autoload', 'psr-4'));
    }

    public function testResolvePsr4Class()
    {
        $composer = new ComposerJson($this->composerLocation);
        $expected = File::class;
        $namespace = $composer->resolvePsr4Class($this->getProjectRootPath('src', 'File.php'));

        $this->assertSame($expected, $namespace);
    }
}
