<?php

namespace PainlessPHP\Filesystem;

use Closure;
use PainlessPHP\Filesystem\Interface\FilesystemFilter;
use PainlessPHP\Filesystem\Filter\ClosureFilesystemFilter;
use ReflectionMethod;
use ReflectionParameter;

class DirectoryIteratorConfig
{
    /**
     * @var array<ReflectionParameter>
     */
    private static array|null $constructorParameters = null;
    private array $attributes;

    public function __construct(
        array $resultFilters = [],
        array $recursionFilters = []
    )
    {
        $this->setAttributes(func_get_args());
    }

    private function setAttributes(array $attributes)
    {
        foreach($this->getConstructorParameters() as $parameter) {
            $value = $attributes[$parameter->getPosition()] ?? $parameter->getDefaultValue();
            $this->setAttribute($parameter->getName(), $value);
        }
    }

    private function setAttribute(string $attribute, mixed $value)
    {
        $mutator = 'mutate' . ucfirst($attribute);
        $this->attributes[$attribute] = method_exists($this, $mutator) ? $this->$mutator($value) : $value;
    }

    /**
     * @return array<ReflectionParameter>
     */
    static private function getConstructorParameters() : array
    {
        if(self::$constructorParameters === null) {
            self::$constructorParameters = self::resolveConstructorParameters();
        }

        return self::$constructorParameters;
    }

    static private function resolveConstructorParameters() : array
    {
        $constructor = new ReflectionMethod(self::class, '__construct');
        return $constructor->getParameters();
    }

    public function __get(string $name)
    {
        return $this->attributes[$name];
    }

    private function mutateRecursionFilters(array $filters) : array
    {
        return $this->validateFiltersArg(...$filters);
    }

    private function mutateResultFilters(array $filters) : array
    {
        return $this->validateFiltersArg(...$filters);
    }

    private function validateFiltersArg(FilesystemFilter|Closure ...$filters) : array
    {
        return array_map(fn($filter) => $filter instanceof Closure ? new ClosureFilesystemFilter($filter) : $filter, $filters);
    }

    public function with(string $attribute, mixed $value) : self
    {
        $clone = clone $this;
        $clone->setAttribute($attribute, $value);
        return $clone;
    }

    public function merge(array|self $config) : self
    {
        return new self([
            ...$this->attributes,
            ...($config instanceof self ? $config->attributes : $config)
        ]);
    }
}
