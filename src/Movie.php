<?php

namespace App;

use Illuminate\Support\Str;

class Movie
{
    protected string $path;
    protected string $filename;
    protected string $name;
    protected ?Ffprobe $ffprobe = null;

    public function __construct(string $file)
    {
        $this->path = $file;
        $this->filename = basename($file);

        $this->parseFilename();
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return Str::slug($this->name);
    }

    public function getFfprobe(): Ffprobe
    {
        if ($this->ffprobe) {
            return $this->ffprobe;
        }

        $this->ffprobe = new Ffprobe($this);

        return $this->ffprobe;
    }

    protected function parseFilename()
    {
        preg_match('/^(.*)\.[a-z0-9]{2,4}$/u', $this->filename, $matches);

        if (! empty($matches)) {
            $this->name = trim($matches[1]);
        }
    }
}
