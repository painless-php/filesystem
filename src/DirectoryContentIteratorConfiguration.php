<?php

namespace PainlessPHP\Filesystem;

use Closure;
use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\Filter\ClosureFilesystemFilter;
use ReflectionMethod;

class DirectoryContentIteratorConfiguration
{
    private static array|null $defaultAttributes = null;
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
        $this->attributes = $this->getDefaultAttributes();

        foreach($attributes as $key => $value) {
            $mutator = 'mutate' . ucfirst($key);
            $this->attributes[$key] = method_exists($this, $mutator) ? $this->$mutator($value) : $value;
        }
    }

    static private function getDefaultAttributes() : array
    {
        if(self::$defaultAttributes === null) {
            self::$defaultAttributes = self::resolveDefaultAttributes();
        }

        return self::$defaultAttributes;
    }

    static private function resolveDefaultAttributes()
    {
        $constructor = new ReflectionMethod(self::class, '__construct');
        $defaults = [];

        foreach($constructor->getParameters() as $parameter) {
            $defaults[$parameter->getName()] = $parameter->getDefaultValue();
        }

        return $defaults;
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
