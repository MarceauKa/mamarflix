<?php

namespace App\Tmdb;

use App\Movie;
use App\Utils\TmdbDb;

class Tmdb
{
    public Movie $movie;
    public ?array $infos = [];
    public ?array $choices = [];

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
        $this->getMovieInfos();

        // Download poster
        $this->getPosterUrl();
    }

    public function getTitle(): string
    {
        return $this->infos['title'] ?? '';
    }

    public function getOriginalTitle(): string
    {
        return $this->infos['original_title'] ?? '';
    }

    public function getVoteAverage(): string
    {
        $note = $this->infos['vote_average'] ?? 0;

        return number_format((float)$note, 2, '.', '');
    }

    public function getPosterUrl(): ?string
    {
        $poster = $this->infos['poster_path'] ?? null;

        if ($poster) {
            $poster = sprintf('https://image.tmdb.org/t/p/w780%s', $poster);

            $name = sprintf('%s.jpg', $this->movie->getSlug());
            $filename = base_path('data/images/' . $name);

            if (false === file_exists($filename)) {
                file_put_contents($filename, file_get_contents($poster));
            }
        }

        return $poster;
    }

    public function getReleaseDate(): string
    {
        return $this->infos['release_date'] ?? '';
    }

    public function getResume(): string
    {
        $resume = $this->infos['overview'] ?? '';

        return str_replace(
            ['"', "\n", "\r", "\t"],
            ['', ' ', ' ', ' '],
            $resume
        );
    }

    public function getGenres(): array
    {
        $genres = [];

        foreach ($this->infos['genres'] ?? [] as $genre) {
            $genres[] = $genre['name'];
        }

        return $genres;
    }

    public function getCasts(): array
    {
        $castsInfos = $this->infos['casts'] ?? [];
        $casts = $castsInfos['cast'] ?? [];
        $casting = [];

        foreach (array_splice($casts, 0, 5) as $cast) {
            $character = str_replace('"', '', $cast['character']);
            $name = str_replace('"', '', $cast['name']);
            $casting[] = sprintf('%s (%s)', $name, $character);
        }

        return $casting;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'original_title' => $this->getOriginalTitle(),
            'date' => $this->getReleaseDate(),
            'resume' => $this->getResume(),
            'genres' => $this->getGenres(),
            'note' => $this->getVoteAverage(),
            'poster' => $this->getPosterUrl(),
            'casting' => $this->getCasts(),
        ];
    }

    protected function getMovieInfos(): self
    {
        $id = TmdbDb::instance()->getId($this->movie->getSlug());

        if ($id) {
            $this->infos = (new RequestMovie($id))->get();
            $this->choices = [];

            return $this;
        }

        $this->infos = [];
        $this->choices = (new RequestSearch($this->movie->getName()))->get();

        return $this;
    }
}
