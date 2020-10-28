<?php

namespace App\Utils;

class CsvWriter
{
    protected string $filename;
    protected string $quotes;
    protected string $separator;
    protected string $newline;
    protected array $lines = [];

    public function __construct(
        string $filename,
        string $quotes = '"',
        string $separator = ',',
        string $newline = "\n"
    ) {
        $this->filename = sprintf('%s.csv', $filename);
        $this->quotes = $quotes;
        $this->separator = $separator;
        $this->newline = $newline;
    }

    public function headers(array $headers): self
    {
        $this->addLine($headers, true);

        return $this;
    }

    public function addLine(array $content, bool $unshift = false): self
    {
        $line = [];

        foreach ($content as $col) {
            $line[] = sprintf('%s%s%s', $this->quotes, $col, $this->quotes);
        }

        $line = implode($this->separator, $line);

        if ($unshift) {
            array_unshift($this->lines, $line);
        } else {
            array_push($this->lines, $line);
        }

        return $this;
    }

    public function addLines(array $lines): self
    {
        foreach ($this->lines as $line) {
            $this->addLine($line);
        }

        return $this;
    }

    public function write(): self
    {
        if (count($this->lines) > 0) {
            $content = implode($this->newline, $this->lines);

            file_put_contents($this->path(), $content);

            return $this;
        }

        return $this;
    }

    public function path(): string
    {
        $dir = base_path('data/exports/');

        if (false === is_dir($dir)) {
            mkdir($dir);
        }

        return sprintf('%s/%s', $dir, $this->filename);
    }
}
