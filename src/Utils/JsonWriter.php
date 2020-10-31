<?php

namespace App\Utils;

class JsonWriter
{
    protected string $filename;
    protected array $entries = [];

    public function __construct(string $filename) {
        $this->filename = sprintf('%s.json', $filename);
    }

    public function push(array $content): self
    {
        $this->entries[] = $content;

        return $this;
    }

    public function write(): self
    {
        if (count($this->entries) > 0) {
            $content = json_encode($this->entries);
            file_put_contents($this->path(), $content);

            return $this;
        }

        return $this;
    }

    public function path(): string
    {
        $dir = base_path('data/');

        if (false === is_dir($dir)) {
            mkdir($dir);
        }

        return sprintf('%s/%s', $dir, $this->filename);
    }
}
