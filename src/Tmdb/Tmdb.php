<?php

namespace App\Tmdb;

use App\Movie;
use App\Utils\TmdbDb;

class Tmdb
{
    public const GENRES = [
        28 => "Action",
        12 => "Aventure",
        16 => "Animation",
        35 => "Comédie",
        80 => "Crime",
        99 => "Documentaire",
        18 => "Drame",
        10751 => "Familial",
        14 => "Fantastique",
        36 => "Histoire",
        27 => "Horreur",
        10402 => "Musique",
        9648 => "Mystère",
        10749 => "Romance",
        878 => "Science-Fiction",
        10770 => "Téléfilm",
        53 => "Thriller",
        10752 => "Guerre",
        37 => "Western"
    ];

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
            $poster = sprintf('https://image.tmdb.org/t/p/w780/%s', $poster);

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

        return str_replace(["\n", '"'], '', $resume);
    }

    public function getGenres(): array
    {
        $ids = $this->infos['genre_ids'] ?? [];
        $genres = [];

        foreach ($ids as $id) {
            if (array_key_exists($id, self::GENRES)) {
                $genres[] = self::GENRES[$id];
            }
        }

        return $genres;
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
