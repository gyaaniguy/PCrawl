<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Response\PResponse;

interface InterfaceHttpClient
{
    //  $defaultOptions = ['user_agent' => '', 'custom_client_options' => '', 'redirect_num' => '', 'https' => 'headers'];

    public function get(string $url, array $options = []): PResponse;

    public function post(string $url, array $options = []): PResponse;

    public function setUserAgent(string $userAgent);

    public function setHeaders(array $headers);

    public function enableCookies(string $cookiePath);

    public function disableCookies();

    public function allowHttps();

    
    public function setCustomClientOptions(array $customClientOptions);

    public function setRedirects(int $num);
}