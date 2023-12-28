<?php

namespace PainlessPHP\Filesystem;

use Closure;
use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\Filter\ClosureFilesystemFilter;

class DirectoryContentIteratorConfiguration
{
    private array $attributes;

    public function __construct(
        bool $returnMapping = false,
        bool $recursive = false,
        array $scanFilters = [],
        array $contentFilters = []
    )
    {
        $this->setAttributes(func_get_args());
    }

    private function setAttributes(array $attributes)
    {
        $this->attributes = [];

        foreach($attributes as $key => $value) {
            $mutator = 'mutate' . ucfirst($key);
            $this->attributes[$key] = method_exists($this, $mutator) ? $this->$mutator($value) : $value;
        }
    }

    public function __get(string $name)
    {
        return $this->attributes[$name];
    }

    private function mutateScanFilters(array $filters) : array
    {
        return $this->validateFiltersArg(...$filters);
    }

    private function mutateContentFilters(array $filters) : array
    {
        return $this->validateFiltersArg(...$filters);
    }

    private function validateFiltersArg(FilesystemFilter|Closure ...$filters) : array
    {
        return array_map(fn($filter) => $filter instanceof Closure ? new ClosureFilesystemFilter($filter) : $filter, $filters);
    }
}
