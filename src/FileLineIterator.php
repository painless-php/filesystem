<?php

namespace Nonetallt\Filesystem;

use Nonetallt\String\Str;

class FileLineIterator implements \SeekableIterator
{
    private $file;
    private $stream;
    private $currentLineNumber;

    public function __construct(File $file)
    {
        $this->file = $file;
        $this->currentLineNumber = 0;
    }

    public function __destruct()
    {
        /* Close stream if open */
        if(is_resource($this->stream)) {
            fclose($this->stream); 
        }
    }

    /**
     * Read lines using user supplied callback function.
     *
     * @param callable $cb Read callback function. Return false to break the
     * read loop.
     *
     * @param bool $readBlank If set to true, lines with only whitespace will
     * also be red.
     *
     * @param bool $stripLineEdings set to true if you want to remove line
     * endings for the returned lines.
     *
     */
    public function read(callable $cb, bool $readBlank = false, bool $stripLineEndings = true)
    {
        $lineNumber = 1;

        foreach($this as $line) {
            /* Skip empty lines when readBlank argument is not used */
            if(! $readBlank && trim($line) === '') continue;

            /* Strip line ending if option is in use */
            if($stripLineEndings && Str::endsWith($line, PHP_EOL)) {
                $line = substr($line, 0, strlen($line) - strlen(PHP_EOL));
            }

            /* Return line content and index to callback */
            if($cb($line, $lineNumber) === false) break;

            $lineNumber++;
        }
    }

    /**
     * Get lines. 
     *
     * @param int $offset How many lines should be skipped from the beginning
     * of the file.
     *
     * @param int $limit How many lines should be returned.
     *
     * @param bool $readBlank Set to false if you want to skip lines with only
     * whitespace.
     *
     * @param bool $stripLineEdings set to true if you want to remove line
     * endings for the returned lines.
     *
     * @return array $lines
     *
     */
    public function get(int $offset = 0, int $limit = null, bool $readBlank = false, bool $stripLineEndings = true) : array
    {
        $lines = [];
        $this->read(function($line, $lineNumber) use ($offset, $limit, &$lines) {

            /* Only capture lines after offset */
            if($lineNumber > $offset) $lines[] = $line;

            /* Stop reading after limit */
            if($limit !== null && count($lines) >= $limit) return false;
        }, $readBlank, $stripLineEndings);

        return $lines;
    }

    public function count(bool $countEmpty = false) : int
    {
        $lineCount = 0;

        foreach($this as $line) {
            /* Skip empty lines when countEmpty argument is not used */
            if(! $countEmpty && trim($line) === '') continue;
            $lineCount++;
        }

        /* Remove one from line count to account for feof operation */
        if($countEmpty) $lineCount--;

        return $lineCount;
    }

    private function getStream()
    {
        if($this->stream === null) {
            $this->stream = $this->file->openStream('r');
        }

        return $this->stream;
    }

    public function seek(int $position) 
    {
        $size = $this->file->getSize();
        if($position > $size) {
            $msg = "Seek position $position is greater than file size $size";
            throw new \OutOfBoundsException($msg);
        }

        // TODO
    }

    public function current()
    {
        $line = new FileLine($this->file, $this->currentLineNumber + 1, ftell($this->getStream()));
        $line->setContent(fgets($this->getStream()));

        return $line;
    }

    public function key()
    {
        return $this->currentLineNumber;
    }

    public function next()
    {
        ++$this->currentLineNumber;
    }

    public function rewind()
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
}
