<?php

namespace PainlessPHP\Filesystem\Interface;

use Iterator;

interface DirectoryContentIterator extends Iterator
{
    public function getPath() : string;
}
