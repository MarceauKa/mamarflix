<?php

namespace App;

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

    protected function makeRequest(): ResponseInterface
    {
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        $base = 'https://api.themoviedb.org/3/search/movie';

        $query = http_build_query([
            'api_key' => Env::get('TMDB_KEY'),
            'lang' => Env::get('TMDB_LANG'),
            'query' => $this->movie->getName(),
            'page' => 1,
        ]);

        $url = sprintf('%s?%s', $base, $query);

        return $client->get($url);
    }
}
