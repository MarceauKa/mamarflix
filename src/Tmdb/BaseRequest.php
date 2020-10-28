<?php

namespace App\Tmdb;

use App\Utils\Env;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class BaseRequest
{
    protected string $url = 'https://api.themoviedb.org/3/';

    abstract protected function getUri(): string;

    protected function getParams(): array
    {
        return [];
    }

    public function request(): ResponseInterface
    {
        $client = new Client([
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);

        $base = sprintf('%s%s', $this->url, $this->getUri());

        $query = http_build_query(
            array_merge([
                'api_key' => Env::get('TMDB_KEY'),
                'lang' => Env::get('TMDB_LANG'),
                'page' => 1,
            ], $this->getParams())
        );

        $url = sprintf('%s?%s', $base, $query);

        return $client->get($url);
    }
}