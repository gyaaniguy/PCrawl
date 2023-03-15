<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\Response;

abstract class AbstractHttpClient
{
    protected Response $res;
    protected array $clientOptions = [];

    public function getOptions(): array
    {
        return $this->clientOptions;
    }

    abstract public function get(string $url): Response;

    abstract public function post(string $url, $postData): Response;

    abstract public function getFile(string $url, array $options = []): Response;
}
