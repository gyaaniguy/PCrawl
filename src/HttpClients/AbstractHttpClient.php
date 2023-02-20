<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;

abstract class AbstractHttpClient
{
    public PResponse $res;
    public array $clientOptions = [];

    public function getOptions(): array
    {
        return $this->clientOptions;
    }

    abstract public function get(string $url, array $requestOptions = []): PResponse;

    abstract public function post(string $url): PResponse;
}