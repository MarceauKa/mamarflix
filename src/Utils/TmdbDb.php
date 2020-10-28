<?php

namespace App\Utils;

class TmdbDb
{
    protected static ?self $instance = null;
    public ?array $db = null;

    private function __construct()
    {
        $this->ensureCreated();
        $this->open();
    }

    public static function instance(): self
    {
        if (is_null(self::$instance)) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    public function setId(string $slug, int $id): bool
    {
        $this->db[$slug] = $id;

        return $this->write();
    }

    public function getId(string $slug): ?int
    {
        if (array_key_exists($slug, $this->db)) {
            return $this->db[$slug];
        }

        return null;
    }

    protected function write(): bool
    {
        return file_put_contents(
            $this->path(),
            json_encode($this->db)
        );
    }

    protected function open(): void
    {
        if (is_null($this->db)) {
            $db = file_get_contents($this->path());

            if ($db) {
                $this->db = \json_decode($db, true);
                return;
            }

            throw new \RuntimeException("Can't open TMDb database");
        }
    }

    protected function ensureCreated(): bool
    {
        if (is_file($this->path())) {
            return true;
        }

        if (file_put_contents($this->path(), \json_encode([]))) {
            return true;
        }

        throw new \RuntimeException("Can't create TMDb database");
    }

    protected function path(): string
    {
        return base_path('data/tmdb.json');
    }
}
