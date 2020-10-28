<?php

namespace App\Tmdb;

use App\Movie;
use App\Utils\Env;
use App\Utils\Cache;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

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

    public function __construct(Movie $movie)
    {
        $this->movie = $movie;
        $this->getMovieInfos();
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

            $filename = sprintf('images/%s.jpg', $this->movie->getSlug());

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
        return $this->infos['overview'] ?? '';
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
        $cacheKey = $this->movie->getSlug() . '.tmdb';

        if (Cache::has($cacheKey)) {
            $infos = Cache::get($cacheKey);
            $this->infos = json_decode($infos, true);

            return $this;
        }

        $response = $this->makeRequest();
        $body = (string)$response->getBody();

        try {
            $infos = json_decode($body, true);
        } catch (\Exception $e) {
            throw new \RuntimeException("Can't decode JSON infos from {$this->movie->getPath()}");
        }
        if (array_key_exists('results', $infos) && count($infos['results']) >= 1) {
            $infos = $infos['results'][0];
        }

        $this->infos = $infos ?? [];
        Cache::set($cacheKey, json_encode($this->infos));

        return $this;
    }
}
