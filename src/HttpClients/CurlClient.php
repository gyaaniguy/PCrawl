<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;

class CurlClient implements InterfaceHttpClient
{
    public PResponse $res;
    public $ch;
    private string $cookiePath;
    public array $responseHeaders = [];

//  public array $defaultOptions = ['user_agent' => '', 'custom_client_options' => ''];

    public function __construct()
    {
        $this->res = new PResponse();
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }

    public function get(string $url, array $options = []): PResponse
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

    public function post(string $url, $postData = []): PResponse
    {
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
        return $this->get($url);
    }

    public function setUserAgent(string $userAgent)
    {
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    public function setHeaders(array $headers)
    {
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function enableCookies(string $cookiePath)
    {
        if (empty($cookiePath)) {
            $this->cookiePath = '/tmp/cook-preRequest-' . uniqid();
        }
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookiePath);
    }

    public function disableCookies()
    {
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
    }

    public function clearCookies()
    {
        if (!empty($this->cookiePath)) {
            unlink($this->cookiePath);
        }
    }

    public function allowHttps()
    {
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function setRedirects(int $num)
    {
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
    }

    public function customClientOptions(array $customClientOptions)
    {
        curl_setopt_array($this->ch, $customClientOptions);
    }
}