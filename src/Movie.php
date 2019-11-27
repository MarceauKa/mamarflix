<?php

namespace App;

class Movie
{
    /** @var string $path */
    public $path;
    /** @var string $filename */
    public $filename;
    /** @var string $name */
    public $name;
    /** @var int $year */
    public $year;
    /** @var Ffmpeg $ffmpeg */
    public $ffmpeg;

    public function __construct(string $file)
    {
        $this->path = $file;
        $this->filename = basename($this->path);

        $this->parseFilename();

        if (!empty($this->name)) {
            $this->ffmpeg = new Ffmpeg($this);
        }
    }

    protected function parseFilename()
    {
        preg_match('/^(.*)\s([0-9]{4})\.[a-z0-9]{2,4}$/u', $this->filename, $matches);

        if (! empty($matches)) {
            $this->name = trim($matches[1]);
            $this->year = $matches[2];
        }
    }

    public function __toString()
    {
        return implode(';', [
            $this->name,
            $this->year,
            $this->ffmpeg->duration,
            $this->ffmpeg->audio,
            $this->ffmpeg->subs,
            $this->ffmpeg->resolution,
            $this->ffmpeg->hdr ? 'Oui' : 'Non',
            $this->ffmpeg->size,
        ]);
    }
}
