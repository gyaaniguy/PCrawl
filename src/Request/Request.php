<?php

namespace Gyaaniguy\PCrawl\Request;

use Exception;
use Gyaaniguy\PCrawl\HttpClients\AbstractHttpClient;
use Gyaaniguy\PCrawl\HttpClients\GuzzleClient;
use Gyaaniguy\PCrawl\Response\Response;
use InvalidArgumentException;

/**
 *  A sample class
 *
 * @author Nikhil Jain
 */
class Request
{
    private Response $lastRawResponse;
    private AbstractHttpClient $httpClient;

    public function __construct(AbstractHttpClient $httpClient = null)
    {
        $this->httpClient = $httpClient ?? (new GuzzleClient());
    }

    /**
     * @throws Exception
     */
    public function getFile(string $url = '', array $options = []): Response
    {
        if (method_exists($this->httpClient, 'getFile') === false) {
            throw new InvalidArgumentException('getFile() method not implemented in ' . get_class($this->httpClient));
        }
        if (!isset($options['file_path'])) {
            throw new InvalidArgumentException('file_path not set in options');
        }
        $fileOptions['file_path'] = $options['file_path'];
        // TODO - this is a hack , the response object body would be empty in case of file download. Implement after implementing guzzleclient getfile
        $this->lastRawResponse = $this->httpClient->getFile($url, $fileOptions);
        return $this->lastRawResponse;
    }

    public function get(string $url, array $requestOptions = []): Response
    {
        try {
            $this->lastRawResponse = $this->httpClient->get($url, $requestOptions);
        } catch (Exception $e) {
            $this->lastRawResponse = new Response();
            $this->lastRawResponse->setError($e->getMessage());
        }
        return $this->lastRawResponse;
    }

    /**
     * @param string $url
     * @param string|array $postData - if array, it will be converted to query string
     * @return Response
     */
    public function post(string $url, $postData): Response
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        try {
            $this->lastRawResponse = $this->httpClient->post($url, $postData);
        } catch (Exception $e) {
            $this->lastRawResponse = new Response();
            $this->lastRawResponse->setError($e->getMessage());
        }
        return $this->lastRawResponse;
    }


    public function setClient(AbstractHttpClient $client): Request
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * @return AbstractHttpClient
     */
    public function getClient(): AbstractHttpClient
    {
        return $this->httpClient;
    }
}
