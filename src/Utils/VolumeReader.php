<?php

namespace App\Utils;

use App\Movie;
use Symfony\Component\Finder\Finder;

class VolumeReader
{
    protected string $path;
    protected array $files = [];

    public function __construct()
    {
        $path = Env::get('VOLUME_PATH');

        if (false === is_readable($path) && is_dir($path)) {
            throw new \InvalidArgumentException("$path is not readable nor a directory");
        }

        $this->path = (string)$path;
        $this->readFiles();
    }

    protected function readFiles(): self
    {
        $finder = new Finder();
        $extensions = explode(',', Env::get('EXTENSIONS', '.mkv,.mp4'));

        $files = $finder
            ->files()
            ->ignoreUnreadableDirs();

        foreach ($extensions as $ext) {
            $files->name(sprintf('*%s', $ext));
        }

        $files = iterator_to_array($files->in($this->path));

        foreach ($files as $file) {
            $this->files[] = new Movie($file->getPathname());
        }

        usort($this->files, fn ($a, $b) => strcmp($a->getName(), $b->getName()));

        return $this;
    }

    public function path(): string
    {
        return $this->path;
    }

    public function count(): int
    {
        return count($this->files);
    }

    /**
     * @return Movie[]|array
     */
    public function files(): array
    {
        return $this->files;
    }
}
