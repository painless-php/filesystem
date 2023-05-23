<?php

namespace PainlessPHP\Filesystem;

use PainlessPHP\Filesystem\Exception\FilesystemException;

class FileLine
{
    private File $file;
    private int $number;
    private int $position;
    private ?string $content;

    public function __construct(File $file, int $lineNumber, int $pointerStartPosition, string $content = null)
    {
        $this->file = $file instanceof File ? $file : new File($file);
        $this->number = $lineNumber;
        $this->position = $pointerStartPosition;
        $this->content = $content;
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
    public function getFilePointerStartPosition() : int
    {
        return $this->position;
    }

    /**
     * Get the file pointer postion for end of the line
     *
     */
    public function getFilePointerEndPosition() : int
    {
        return $this->position + mb_strlen($this->getContent());
    }

    /**
     * Get the line number
     *
     */
    public function getLineNumber() : int
    {
        return $this->number;
    }

    /**
     * Get line content
     *
     */
    public function getContent() : string
    {
        if($this->content === null) {
            $this->content = $this->loadContent();
        }

        return $this->content;
    }

    /**
     * Override the file content for this line
     *
     */
    public function writeContent(string $content)
    {
        // TODO support editing in-memory

        $tmp = tmpfile();
        $meta = stream_get_meta_data($tmp);

        foreach($this->file as $line) {
            if($line->getLineNumber() === $this->number) {
                fwrite($tmp, $content);
            }
            fwrite($tmp, (string)$line);
        }

        $tempFile = new File($meta['uri']);
        $this->file->writeLines($tempFile->readLines());

        fclose($tmp);
    }

    /**
     * Open stream at the position of the line
     *
     */
    public function openStream(FilesystemStreamMode $mode)
    {
        $stream = $this->file->openStream($mode);

        if(fseek($stream, $this->position) === -1) {
            $path = $this->file->getPathname();
            $msg = "Could not open stream at offset $this->position of '$path'";
            throw new FilesystemException($msg, $this->file->getPathname());
        }

        return $stream;
    }

    private function loadContent() : string
    {
        $stream = $this->openStream(FilesystemStreamMode::Read);
        $content = fgets($stream);

        if($content === false) {
            $path = $this->file->getPathname();
            $msg = "Could not read content at offset $this->position of '$path'";
            throw new FilesystemException($msg);
        }

        fclose($stream);
        return $content;
    }
}
