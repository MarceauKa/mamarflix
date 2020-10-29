<?php

namespace App\Tmdb;

use GuzzleHttp\Psr7\Response;

class RequestMovie extends BaseRequest
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    public function get(): ?array
    {
        if ($this->hasCache()) {
            return $this->getCache();
        }

        $response = $this->request();
        $body = (string)$response->getBody();
        $result = json_decode($body, true);

        if (false === array_key_exists('title', $result)) {
            return null;
        }

        $this->setCache($result);

        return $result;
    }

    protected function getUri(): string
    {
        return 'movie/' . $this->id;
    }

    protected function getParams(): array
    {
        return [
            'append_to_response' => 'casts',
        ];
    }

    protected function cacheKey(): string
    {
        return sprintf('movie-%d.json', $this->id);
    }
}