<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;

class CurlClient extends CurlBaseClient
{
    private string $cookiePath;

    public function __construct()
    {
        parent::__construct();
    }

    public function setUserAgent(string $userAgent): CurlClient 
    {
        $this->curlInitIf();
        $this->clientOptions['user_agent'] = $userAgent;
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
        return $this;
    }

    public function setHeaders(array $headers): CurlClient 
    {
        $this->curlInitIf();
        $this->clientOptions['headers'] = $headers;
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        return $this;
    }

    public function enableCookies(string $cookiePath): CurlClient
    {
        $this->curlInitIf();
        if (empty($cookiePath)) {
            $this->cookiePath = '/tmp/' . uniqid(rand(999, 99999999), true);
        }
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookiePath);
        return $this;
    }

    public function disableCookies(): CurlClient
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
        return $this;
    }

    public function strictHttps(bool $enable): CurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['strict_https'] = $enable;
        if ($enable) {
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);
            curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, true);
            return $this;
        } 
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        return $this;
    }

    public function setRedirects(int $num): CurlClient
    {
        $this->curlInitIf();
        $this->clientOptions['redirect_num'] = $num;
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
        return $this;
    }


}