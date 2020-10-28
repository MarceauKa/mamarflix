<?php

namespace App\Tmdb;

class RequestSearch extends BaseRequest
{
    protected string $query;

    public function __construct(string $query)
    {
        $this->query = $query;
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

}