<?php

namespace App;

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
            $content = '';

            if ($this->headers) {
                $content .= $this->headers . $this->newline;
            }

            $content .= implode($this->newline, $this->lines);

            file_put_contents($this->path(), $content);

            return $this;
        }
    }

    protected function path(): string
    {
        $dir = __DIR__ . '/../exports/';

        if (false === is_dir($dir)) {
            mkdir($dir);
        }

        return $dir . $this->filename;
    }
}
