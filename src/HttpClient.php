<?php

namespace Gyaaniguy\PCrawl;

use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;

abstract class HttpClient
{
    public Client $client ;

    abstract function get($url, $options = []): PResponse;        
    abstract function post($url, $options = []): PResponse;

    abstract function setUserAgent(string $userAgent);
    abstract function setHeaders(array $headers );
    abstract function enableCookies(string $cookiePath);
    abstract function disableCookies();
    abstract function clearCookies();
    abstract function allowHttps();
    abstract function setRedirects(int $num);    
}