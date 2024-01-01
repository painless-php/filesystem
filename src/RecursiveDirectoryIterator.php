<?php

namespace PainlessPHP\Filesystem;

use RecursiveIteratorIterator;
use PainlessPHP\Filesystem\DirectoryIteratorConfig;
use PainlessPHP\Filesystem\Internal\Iterator\InternalFilterIterator;
use PainlessPHP\Filesystem\Internal\Iterator\InternalRecursiveDirectoryIterator;
use PainlessPHP\Filesystem\Internal\Iterator\InternalRecursiveFilterIterator;

class RecursiveDirectoryIterator extends InternalFilterIterator
{
    public function __construct(string $path, array|DirectoryIteratorConfig $config = [])
    {
        parent::__construct(
            path: $path,
            innerIterator: new RecursiveIteratorIterator(
                new InternalRecursiveFilterIterator(
                    new InternalRecursiveDirectoryIterator($path),
                    $path,
                    $config
                )
            ),
            config: $config
        );
    }

    /**
     * @override
     */
    public function current(): mixed
    {
        // TODO map returned values
        return parent::current();
    }
}
