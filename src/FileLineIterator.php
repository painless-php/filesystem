<?php

namespace PainlessPHP\Filesystem;

use OutOfBoundsException;
use SeekableIterator;

class FileLineIterator implements SeekableIterator
{
    private File $file;
    private mixed $stream;
    private int $currentLineNumber;
    private bool $readEmptyLines;
    private bool $stripLineEndings; // TODO

    public function __construct(File|string $file, $readEmptyLines = false, bool $stripLineEndings = true)
    {
        $this->file = $file instanceof File ? $file : new File($file);
        $this->readEmptyLines = $readEmptyLines;
        $this->stripLineEndings = $stripLineEndings;
        $this->currentLineNumber = 0;
    }

    public function __destruct()
    {
        /* Close stream if open */
        if(is_resource($this->stream)) {
            fclose($this->stream);
        }
    }

    public function count() : int
    {
        $lineCount = 0;

        foreach($this as $line) {
            /* Skip empty lines when countEmpty argument is not used */
            if(! $this->readEmptyLines && trim($line) === '') {
                continue;
            }
            $lineCount++;
        }

        /* Remove one from line count to account for feof operation */
        if($this->readEmptyLines) {
            $lineCount--;
        }

        return $lineCount;
    }

    public function seek(int $offset) : void
    {
        $size = $this->file->getSize();

        if($offset > $size) {
            $msg = "Seek position {$offset} is greater than file size {$size}";
            throw new OutOfBoundsException($msg);
        }

        // TODO
    }

    public function current(): mixed
    {
        $line = new FileLine($this->file, $this->currentLineNumber + 1, ftell($this->getStream()));
        $line->setContent(fgets($this->getStream()));

        return $line;
    }

    public function key(): mixed
    {
        return $this->currentLineNumber;
    }

    public function next() : void
    {
        $this->currentLineNumber++;
    }

    public function rewind() : void
    {
        $this->currentLineNumber = 0;
        $this->stream = null;
    }

    public function valid() : bool
    {
        /* Feof check reads always one empty line at the end of the file */
        return ftell($this->getStream()) < $this->file->getSize();
    }

    public function getFile() : File
    {
        return $this->file;
    }

    private function getStream()
    {
        if($this->stream === null) {
            $this->stream = $this->file->openStream(FilesystemStreamMode::Read);
        }

        return $this->stream;
    }
}
