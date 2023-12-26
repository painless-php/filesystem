<?php

namespace Test\Concern;

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
