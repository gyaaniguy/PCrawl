<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Response\PResponse;

interface InterfaceHttpClient
{

    public function get(string $url, array $options = []): PResponse;

    public function post(string $url, array $options = []): PResponse;

    public function setUserAgent(string $userAgent);

    public function setHeaders(array $headers);

    public function enableCookies(string $cookiePath);

    public function disableCookies();

    public function clearCookies();

    public function allowHttps();
    public function customClientOptions(array $customClientOptions);

    public function setRedirects(int $num);
}