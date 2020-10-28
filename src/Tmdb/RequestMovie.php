<?php

namespace App\Tmdb;

class RequestMovie extends BaseRequest
{
    protected int $id;

    public function __construct(int $id)
    {
        $this->id = $id;
    }

    protected function getUri(): string
    {
        return 'movie/' . $this->id;
    }
}