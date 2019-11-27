<?php

namespace App;

class Reader
{
    /** @var array $files */
    public $files;

    public function __construct(string $path, int $limit = null, int $offset = null)
    {
        $files = glob($path . '*');
        $this->files = array_slice($files, $offset ?? 0, $limit ?? count($files));
    }

    public function count(): int
    {
        return count($this->files);
    }

    public function parse(): void
    {
        foreach ($this->files as $file) {
            $this->files[] = new Movie($file);
        }
    }
}
