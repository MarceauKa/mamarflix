<?php

namespace App;

use Illuminate\Support\Str;

class Movie
{
    protected string $path;
    protected string $filename;
    protected string $name;
    protected ?Ffprobe $ffprobe = null;
    protected ?Tmdb $tmdb = null;

    public function __construct(string $file)
    {
        $this->path = $file;
        $this->filename = basename($file);
        $this->name = $this->filename;

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
        return Str::slug($this->filename);
    }

    public function getFfprobe(): Ffprobe
    {
        if ($this->ffprobe) {
            return $this->ffprobe;
        }

        $this->ffprobe = new Ffprobe($this);

        return $this->ffprobe;
    }

    public function getTmdb(): Tmdb
    {
        if ($this->tmdb) {
            return $this->tmdb;
        }

        $this->tmdb = new Tmdb($this);

        return $this->tmdb;
    }

    protected function parseFilename(): void
    {
        preg_match('/^(.*)\s(\d{4})\.[a-z0-9]{2,4}$/u', $this->filename, $matches);

        if (! empty($matches)) {
            $this->name = trim($matches[1]);
        }
    }
}
