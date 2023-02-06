<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;

class CurlClient implements InterfaceHttpClient
{
    public PResponse $res;
    public $ch;
    public array $responseHeaders = [];
    private string $cookiePath;


    public function __construct()
    {
        $this->res = new PResponse();
        $this->curl_init_if();
    }

    public function get(string $url, array $options = []): PResponse
    {
        $this->curl_init_if();
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
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
        return $this->get($url);
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    public function setHeaders(array $headers): void
    {
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function enableCookies(string $cookiePath): void
    {
        $this->curl_init_if();
        if (empty($cookiePath)) {
            $this->cookiePath = '/tmp/' . uniqid(rand(999,99999999),true);
        }        
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookiePath);
    }

    public function disableCookies(): void
    {
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
    }

    public function allowHttps(): void
    {
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function setRedirects(int $num): void
    {
        $this->curl_init_if();
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
    }

    public function setCustomClientOptions(array $customClientOptions): void
    {
        $this->curl_init_if();
        curl_setopt_array($this->ch, $customClientOptions);
    }

    public function close(): void
    {
        curl_close($this->ch);
    }

    public function curl_init_if(): void
    {
        if (!$this->ch || !is_resource($this->ch)) {
            $this->ch = curl_init();
            $this->enableReturnTransfer();
        }
    }

    /**
     * @return void
     */
    public function enableReturnTransfer(): void
    {
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
    }
}