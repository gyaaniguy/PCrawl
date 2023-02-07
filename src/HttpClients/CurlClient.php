<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;

class CurlClient implements InterfaceHttpClient
{
    public PResponse $res;
    public $ch;
    public array $responseHeaders = [];
    private string $cookiePath;


    public function __construct()
    {
        $this->res = new PResponse();
        $this->curlInitIf();
    }

    public function get(string $url, array $options = []): PResponse
    {
        $this->curlInitIf();
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
        return $this->setResponse($url, $curlRes);
    }

    public function post(string $url, $postData = []): PResponse
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
        return $this->get($url);
    }

    public function setUserAgent(string $userAgent): void
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_USERAGENT, $userAgent);
    }

    public function setHeaders(array $headers): void
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
    }

    public function enableCookies(string $cookiePath): void
    {
        $this->curlInitIf();
        if (empty($cookiePath)) {
            $this->cookiePath = '/tmp/' . uniqid(rand(999, 99999999), true);
        }
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, $cookiePath);
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, $cookiePath);
    }

    public function disableCookies(): void
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_COOKIEJAR, '');
        curl_setopt($this->ch, CURLOPT_COOKIEFILE, '');
    }

    public function allowHttps(): void
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
    }

    public function setRedirects(int $num): void
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_MAXREDIRS, $num);
    }

    public function setCustomClientOptions(array $customClientOptions): void
    {
        $this->curlInitIf();
        curl_setopt_array($this->ch, $customClientOptions);
    }

    public function close(): void
    {
        curl_close($this->ch);
    }

    public function curlInitIf(): void
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

    /**
     * Download a file.
     * @param string $url Url of the file to download
     * @param array $options 'filepath' key should have the value of the path to save the file
     * @return PResponse
     * @throws InvalidArgumentException
     */
    public function getFile(string $url, array $options = []): PResponse
    {
        if (empty($options) || empty($options['filepath'])) {
            throw new InvalidArgumentException ('No filepath provided');
        }
        $fp = fopen($options['filepath'], 'w+');
        if (!$fp) {
            throw new InvalidArgumentException ('filepath is not writable');
        }
        $chFile = curl_copy_handle($this->ch);
        curl_setopt($chFile, CURLOPT_FILE, $fp);
        $curlRes = curl_exec($chFile);
        return $this->setResponse($url, $curlRes);
    }

    /**
     * @param string $url
     * @param $curlRes
     * @return PResponse
     */
    public function setResponse(string $url, $curlRes): PResponse
    {
        $this->res->setRequestUrl($url);
        $this->res->setBody($curlRes);
        $this->res->setError(curl_error($this->ch));
        $this->res->setHttpCode(curl_getinfo($this->ch, CURLINFO_HTTP_CODE));
        $this->res->setLastUrl(curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL));
        $this->res->setResponseHeaders($this->responseHeaders);
        return $this->res;
    }
}