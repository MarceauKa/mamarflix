<?php

namespace App\Tmdb;

use App\Utils\Cache;
use App\Utils\Env;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class BaseRequest
{
    protected string $url = 'https://api.themoviedb.org/3/';

    abstract public function get(): ?array;

    abstract protected function getUri(): string;

    abstract protected function cacheKey(): string;

    abstract protected function getParams(): array;

    protected function request(): ResponseInterface
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
                'language' => Env::get('TMDB_LANG'),
                'page' => 1,
            ], $this->getParams())
        );

        $url = sprintf('%s?%s', $base, $query);

        return $client->get($url);
    }

    protected function hasCache(): bool
    {
        return Cache::has($this->cacheKey());
    }

    protected function setCache(?array $content): void
    {
        if (empty($content)) {
            return;
        }

        Cache::set($this->cacheKey(), json_encode($content));
    }

    protected function getCache(): array
    {
        $data = Cache::get($this->cacheKey());

        return \json_decode($data, true);
    }
}