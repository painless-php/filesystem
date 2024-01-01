<?php

namespace PainlessPHP\Filesystem\Internal\Trait;

use PainlessPHP\Filesystem\DirectoryIteratorConfig;

trait HasConfig
{
    private DirectoryIteratorConfig $config;

    private function setConfig(array|DirectoryIteratorConfig $config)
    {
        if(is_array($config)) {
            $config = new DirectoryIteratorConfig(...$config);
        }

        $this->config = $config;
    }

    public function getConfig() : DirectoryIteratorConfig
    {
        return $this->config;
    }
}
