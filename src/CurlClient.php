<?php

namespace Gyaaniguy\PCrawl;

use GuzzleHttp\Client;

class CurlClient extends HttpClient
{
    public PResponse $res;
    public $ch;
    private string $cookiePath;
    public array $responseHeaders = [];

    public function __construct()
    {
        $this->res = new PResponse();
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    function get($url, $options = []): PResponse
    {
        curl_setopt($this->ch, CURLOPT_URL, $url);
        // this function is called by curl for each header received
        curl_setopt(
            $this->ch, CURLOPT_HEADERFUNCTION,
            function ($curl, $header) {
                $len = strlen($header);
                $header = explode(':', $header, 2);
                if (count($header) < 2) {
                    return $len;
                }

                $this->responseHeaders[strtolower(trim($header[0]))][] = trim($header[1]);
                return $len;
            }
        );

        $curlRes = curl_exec($this->ch);
        $this->res->setRequestUrl($url);
        $this->res->setBody($curlRes);
        $this->res->setError(curl_error($this->ch));
        $this->res->setHttpCode(curl_getinfo($this->ch, CURLINFO_HTTP_CODE));
        $this->res->setLastUrl(curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL));
        $this->res->setResponseHeaders($this->responseHeaders);
        return $this->res;
    }

    function post($url, $options = []): PResponse
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $options);
        return $this->get($url);
    }

    function setUserAgent(string $userAgent)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    function setHeaders(array $headers)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    function enableCookies(string $cookiePath)
    {
        if (empty($cookiePath)) {
            $this->cookiePath = '/tmp/cook-prequest-' . uniqid();
        }
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookiePath);
    }

    function disableCookies()
    {
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
    }

    function clearCookies()
    {
        if (!empty($this->cookiePath)) {
            unlink($this->cookiePath);
        }
    }

    function allowHttps()
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    function setRedirects(int $num)
    {
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
    }
}