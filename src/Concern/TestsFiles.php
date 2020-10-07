<?php

namespace Nonetallt\File\Concern;

use Nonetallt\String\Str;

trait TestsFiles
{
    private $basePath;

    /**
     * @before
     * @after
     *
     */
    protected function cleanOutput()
    {
        $exclude = [
            '.', 
            '..',
            '.gitignore',
            '.keep'
        ];

        foreach(array_diff(scandir($this->getTestPath('output')), $exclude) as $filename) {
             unlink($this->getTestPath("output/$filename"));
        }
    }

    /**
     * Try to guess the project base path.
     *
     * @return string $path
     * 
     */
    private function guessBasePath() : string
    {
        $path = getcwd();

        if($path === false) {
            $msg = "Could not set base path using getcwd()";
            throw new FilesystemException($msg);
        }

        return $path;
    }

    private function appendToPath(string $path, ?string $append) : string
    {
        if($append !== null && ! Str::startsWith($append, DIRECTORY_SEPARATOR)) {
            $append = DIRECTORY_SEPARATOR . $append;
            $path .= $append;
        } 

        return $path;
    }

    public function getBasePath(string $append = null)
    {
        if($this->basePath === null) {
            $this->basePath = $this->guessBasePath();
        }

        return $this->appendToPath($this->basePath, $append);
    }

    public function getTestPath(string $append = null)
    {
        $choices = ['test', 'tests'];
        $options = [];

        foreach($choices as $dir) {
            $path = $this->getBasePath($dir);
            $options[] = $path;
            if(is_dir($path)) {
                return $this->appendToPath($path, $append);
            }
        }

        $options = implode(PHP_EOL, $options);
        $msg = "Could not find a valid testing directory from following choices:" . PHP_EOL . $options;
        throw new FilesystemException($msg, $this->getBasePath());
    }
}
