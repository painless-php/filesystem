<?php

namespace PainlessPHP\Filesystem;

use InvalidArgumentException;
use JsonException;
use PainlessPHP\Filesystem\Exception\FilesystemException;
use PainlessPHP\Filesystem\Internal\StringHelpers;

class ComposerJson extends File
{
    private array|null $parsed;

    public function __construct(string $path)
    {
        parent::__construct($path);
        $this->parsed = null;
    }

    public static function locate(string $dir) : self
    {
        return new self(Filesystem::findUpwards($dir, 'composer.json'));
    }

    public function resolvePsr4Class(string|FilesystemObject $file) : string
    {
        if(is_string($file)) {
            $file = new File($file);
        }

        /** @var File $file  */
        if(! $file->hasExtension('php')) {
            $msg = 'Target file lacks the .php extension';
            throw new InvalidArgumentException($msg);
        }

        $autoloadPaths = $this->getParsedContent('autoload', 'psr-4');

        foreach ($autoloadPaths as $namespaceRoot => $relativeNamespacePath) {
            $namespacePath = Filesystem::appendToPath($this->getPath(), $relativeNamespacePath);
            if(! str_starts_with($file->getPathname(), $namespacePath)) {
                continue;
            }

            $relativePath = StringHelpers::removeSuffix($file->getRelativePath($namespacePath), '.php');
            return $namespaceRoot . implode('\\', explode(DIRECTORY_SEPARATOR, $relativePath));
        }

        $msg = "Could not find valid psr-4 namespace for '{$file->getPathname()}'";
        throw new FilesystemException($msg);
    }

    public function getParsedContent(string ...$keys) : mixed
    {
        if($this->parsed === null) {
            $this->parsed = $this->parseContents();
        }

        $result = $this->parsed;
        $currentPath = '';

        foreach($keys as $index => $key) {
            $value = $result[$key] ?? null;
            $currentPath .= ($index > 0 ? '.' : '') . $key;

            if($value === null) {
                $msg = "Could not find path '$currentPath' in {$this->getPathname()}";
                throw new FilesystemException($msg);
            }

            $result = $result[$key];
        }

        return $result;
    }

    private function parseContents() : array
    {
        try {
            return json_decode($this->getContents(), true, JSON_THROW_ON_ERROR);
        }
        catch(JsonException $e) {
            throw new FilesystemException(
                message: $e->getMessage(),
                previous: $e
            );
        }
    }
}
