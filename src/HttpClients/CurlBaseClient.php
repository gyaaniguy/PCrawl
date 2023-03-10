<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Response\Response;
use InvalidArgumentException;

class CurlBaseClient extends AbstractHttpClient
{
    protected $ch;
    private array $responseHeaders;

    public function __construct()
    {
        $this->res = new Response();
        $this->curlInitIf();
    }

    /**
     * Create curl_instance if none exists.
     * Set some basic options.
     */
    public function curlInitIf(): void
    {
        if (!$this->ch || !is_resource($this->ch)) {
            $this->ch = curl_init();
            curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($this->ch, CURLOPT_ENCODING, '');
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

    public function post(string $url, $postData): Response
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_POST, 1);
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postData);
        return $this->get($url);
    }

    public function get(string $url): Response
    {
        $this->curlInitIf();
        curl_setopt($this->ch, CURLOPT_URL, $url);
        // this function is called by curl for each header received
        curl_setopt(
            $this->ch,
            CURLOPT_HEADERFUNCTION,
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

    /**
     * Set various fields of the response object.
     * @param string $url
     * @param $response
     * @return Response
     */
    public function setResponse(string $url, $response): Response
    {
        $getInfo = curl_getinfo($this->ch);
        $this->res->setRequestUrl($url);
        $this->res->setBody($response);
        $this->res->setHttpCode($getInfo["http_code"]);
        $this->res->setLastUrl(curl_getinfo($this->ch, CURLINFO_EFFECTIVE_URL) ?? $url);
        $this->res->setResponseHeaders($this->responseHeaders ?? []);
        return $this->res;
    }

    public function close(): void
    {
        curl_close($this->ch);
    }

    /**
     * Download a file.
     * @param string $url Url of the file to download
     * @param array $options 'filepath' key should have the value of the path to save the file
     * @return Response
     * @throws InvalidArgumentException
     */
    public function getFile(string $url, array $options = []): Response
    {
        if (empty($options) || empty($options['file_path'])) {
            throw new InvalidArgumentException('No file_path provided');
        }
        $fp = fopen($options['file_path'], 'w+');
        if (!$fp) {
            throw new InvalidArgumentException('filepath is not writable');
        }
        $chFile = curl_copy_handle($this->ch);
        curl_setopt($chFile, CURLOPT_FILE, $fp);
        $curlRes = curl_exec($chFile);
        return $this->setResponse($url, $curlRes);
    }

    public function closeConnection(): CurlBaseClient
    {
        if ($this->ch && is_resource($this->ch)) {
            curl_close($this->ch);
        }
        return $this;
    }
}
