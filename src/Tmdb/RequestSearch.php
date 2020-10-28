<?php

namespace App\Tmdb;

use Illuminate\Support\Str;

class RequestSearch extends BaseRequest
{
    protected string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
    }

    public function get(): ?array
    {
        if ($this->hasCache()) {
            return $this->getCache();
        }

        $response = $this->request();
        $body = (string)$response->getBody();
        $result = json_decode($body, true);

        if (false === array_key_exists('results', $result)
            || empty($result['results'])) {
            return null;
        }

        $result = $result['results'][0];

        $this->setCache($result);

        return $result;
    }

    protected function getUri(): string
    {
        return 'search/movie';
    }

    protected function getParams(): array
    {
        return [
            'query' => $this->query,
        ];
    }

    protected function cacheKey(): string
    {
        return sprintf('search-%s.json', Str::slug($this->query));
    }
}