<?php

namespace PainlessPHP\Filesystem;

enum FilesystemStreamMode : string
{
    case Read = 'r';
    case ReadWrite = 'r+';
    case Write = 'w';
    case WriteRead = 'w+';
    case WriteAppend = 'a';
    case WriteReadAppend = 'a+';
    case WriteNew = 'x';
    case WriteReadNew = 'x+';
    case Overwrite = 'c';
    case OverwriteRead = 'c+';
    case CloseOnExec = 'e';
}
