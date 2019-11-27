<?php

namespace App;

class Writer
{
    /** @var Reader $reader */
    public $reader;
    /** @var array $output */
    public $output;

    public function __construct(Reader $reader)
    {
        $this->reader = $reader;
        $this->generate();
    }

    protected function generate(): void
    {
        foreach ($this->reader->files as $movie) {
            $this->output[] = (string)$movie;
        }
    }

    public function write(string $filename): void
    {
        if ($this->output) {
            $content = implode("\n", $this->output);
            file_put_contents($this->path($filename), $content);
            return;
        }

        throw new \RuntimeException("No output to write.");
    }

    protected function path(string $filename): string
    {
        $dir = __DIR__ . '/../exports/';

        if (false === is_dir($dir)) {
            mkdir($dir);
        }

        return $dir . $filename;
    }
}
