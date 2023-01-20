<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Response\PResponse;

abstract class AbstractHttpClient
{
    public Client $client;

    abstract public function get($url, $options = []): PResponse;

    abstract public function post(
        $url,
        $options = []
    ): PResponse;

    abstract public function setUserAgent(string $userAgent);

    abstract public function setHeaders(array $headers);

    abstract public function enableCookies(string $cookiePath);

    abstract public function disableCookies();

    abstract public function clearCookies();

    abstract public function allowHttps();

    abstract public function setRedirects(int $num);
}