<?php

namespace Nonetallt\Filesystem\Concern;

use Nonetallt\String\Str;
use Nonetallt\Filesystem\Filesystem;

/**
 * Trait that can be attached to test classes that test filesystem
 *
 * Cleans test/output or tests/output directory before and after each test and
 * provides test path helpers
 *
 */
trait DeletesTestOutput
{
    private $basePath;

    /**
     * @before
     * @after
     *
     */
    protected function deleteTestOutput()
    {
        $this->deleteDirectory(Filesystem::testDirectoryPath('output'), false);
    }

    private function deleteDirectory(string $dirPath, bool $deleteDir)
    {
        $exclude = [
            '.', 
            '..',
            '.gitignore',
            '.keep'
        ];

        foreach(array_diff(scandir($dirPath), $exclude) as $relativePath) {
            $realPath = "$dirPath/$relativePath";

            if(is_dir($realPath)) {
                $this->deleteDirectory($realPath, true);
            }

            if(is_file($realPath)) {
                unlink($realPath);
            }
        }

        if($deleteDir) {
            rmdir($dirPath);
        }
    }
}
