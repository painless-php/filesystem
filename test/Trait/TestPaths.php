<?php

namespace Test\Trait;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\Filesystem;

trait TestPaths
{
    /**
     * Get the path of input for this test
     */
    private function getTestInputPath(string ...$append) : string
    {
        return Filesystem::appendToPath(dirname(__DIR__), 'input', $this->getTestedClassShortName(), ...$append);
    }

    public function getOutputPath(string ...$append)
    {
        return Filesystem::appendToPath(dirname(__DIR__), 'output', ...$append);
    }

    private function cleanOutput()
    {
        Directory::createFromPath($this->getOutputPath())->deleteContents(
            recursive: true,
            exclude: ['.gitignore']
        );
    }

    private function levelThreeDirsPath() : string
    {
        return $this->getTestInputPath('level_3_dirs');
    }

    private function getTestedClassShortName() : string
    {
        $nsParts = explode('\\', get_class($this));
        $class = $nsParts[count($nsParts) - 1];

        return substr($class, 0, strpos($class, 'Test'));
    }
}
