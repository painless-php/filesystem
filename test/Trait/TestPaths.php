<?php

namespace Test\Trait;

use PainlessPHP\Filesystem\Directory;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Filesystem;
use PainlessPHP\Filesystem\FilesystemObject;

trait TestPaths
{
    private function getProjectRootPath(string ...$append)
    {
        return Filesystem::appendToPath(dirname(dirname(__DIR__)), ...$append);
    }

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
        // Directory::createFromPath($this->getOutputPath())->deleteContents(
        //     recursive: true,
        //     config: new DirectoryIteratorConfig(
        //         resultFilters: [
        //             fn(FilesystemObject $file) => $file->getFilename() !== '.gitignore'
        //         ]
        //     )
        // );

        $outputDir = $this->getOutputPath();
        $gitIgnorePath = "$outputDir/.gitignore";

        $ignoreContent = file_get_contents($gitIgnorePath);
        $cmd = "rm -r $outputDir";
        exec($cmd);

        mkdir($outputDir);
        file_put_contents($gitIgnorePath, $ignoreContent);
    }

    private function levelThreeDirsPath() : string
    {
        return $this->getTestInputPath('level_3_dirs');
    }

    private function levelThreeDirsContents()
    {
        return [
            '1',
            '2',
            '3',
            'file_in_base_dir.txt',
            'file_in_dir_1.txt',
            'file_in_dir_2.txt',
            'file_in_dir_3.txt'
        ];
    }

    private function getTestedClassShortName() : string
    {
        $nsParts = explode('\\', get_class($this));
        $class = $nsParts[count($nsParts) - 1];

        return substr($class, 0, strpos($class, 'Test'));
    }

    private function assertIterableMatchesContent(array $expected, Iterable $iterable, callable|string|null $mapping = null)
    {
        $files = [];

        if($mapping === 'filename') {
            $mapping = fn($file) => $file->getFilename();
        }

        foreach($iterable as $file) {
            $files[] = is_null($mapping) ? $file : $mapping($file);
        }

        // Assert that array contents are the same, disregarding keys
        $this->assertEqualsCanonicalizing($expected, $files);
    }

    public function assertOutputDirectoryContentsMatch(array $expected, callable|string|null $mapping = null)
    {
        $outputPath = $this->getOutputPath();
        $iter = Directory::createFromPath($outputPath)->getIterator(
            recursive: true,
            config: new DirectoryIteratorConfig(
                resultFilters: [
                    fn(FilesystemObject $file) => $file->getRelativePath($outputPath) !== '.gitignore'
                ]
            )
        );

        return $this->assertIterableMatchesContent($expected, $iter, $mapping);
    }
}
