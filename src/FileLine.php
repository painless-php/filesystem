<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;

class FileLine
{
    private $file;
    private $number;
    private $position;
    private $content;

    public function __construct(File $file, int $number, int $position)
    {
        $this->file = $file;
        $this->number = $number;
        $this->position = $position;
    }

    /**
     * Get line content
     *
     */
    public function __toString() : string
    {
        return $this->getContent();
    }

    /**
     * Get the file pointer position for start of the line
     *
     */
    public function getStartPosition() : int
    {
        return $this->position;
    }

    /**
     * Get the file pointer postion for end of the line
     *
     */
    public function getEndPosition() : int
    {
        return $this->position + strlen($this->getContent());
    }

    /**
     * Get the line number
     *
     */
    public function getNumber() : int
    {
        return $this->number;
    }

    /**
     * Set line content
     *
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * Get line content
     *
     */
    public function getContent() : string
    {
        if($this->content === null) {
            $stream = $this->openStream('r');
            $content = fgets($stream);

            if($content === false) {
                $msg = "Could not read content at offset $this->position";
                throw new FilesystemException($msg, $this->file->getPath());
            }

            fclose($stream);
        }

        return $this->content;
    }

    /**
     * Override the file content for this line
     *
     */
    public function writeContent(string $content)
    {
        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);

        foreach($this->file as $line) {
            if($line->getNumber() === $this->number) {
                fwrite($tmp, $content);
            }
            fwrite($tmp, (string)$line);
        }

        $tempFile = new File($meta['uri']);
        $this->file->writeLines($tempFile->getLines());

        fclose($tmp);
    }

    /**
     * Open stream at the pointer position
     *
     */
    public function openStream(string $mode)
    {
        $stream = $this->file->openStream($mode);

        if(fseek($stream, $this->position) === false) {
            $msg = "Could not open stream at offset $this->position";
            throw new FilesystemException($msg, $this->file->getPath());
        }

        return $stream;
    }
}
