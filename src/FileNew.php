<?php

namespace Nonetallt\File;

class FileNew extends FilesystemObject
{
    /**
     * Get extension of the file
     *
     * @return string|null $extension
     *
     */
    public function getExtension() : ?string
    {
        $parts = explode('.', $this->pathname);
        $partsCount = count($parts);

        /* File does not have an extension */
        if($partsCount < 2) return null;

        return $parts[$partsCount - 1];
    }
}

