<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Gyaaniguy\PCrawl\Helpers\RegexStuff;
use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class GuzzleBaseClient extends AbstractHttpClient
{
    public Client $guzzleClient;

    public function __construct()
    {
        $this->res = new PResponse();
    }

    /**
     * @param string $url
     * @return PResponse
     * @throws GuzzleException
     */
    public function get(string $url, array $requestOptions = []): PResponse
    {
        $this->getGuzzleClient();
        $response = $this->guzzleClient->request('GET', $url);
        return $this->setResponse($url, $response);
    }

    public function getGuzzleClient()
    {
        if (!isset($this->guzzleClient)) {
            $requestClientOptions = [];
            $requestClientOptions['allow_redirects'] = $this->setRedirectOptions();
            if (isset($this->clientOptions['headers'])) {
                $requestClientOptions['headers'] = RegexStuff::headerToAssoc($this->clientOptions['headers']);
            }
            if (!empty($this->clientOptions['enable_cookies'])) {
                $requestClientOptions['cookies'] = true;
            }
            if (!empty($this->clientOptions['disable_cookies'])) {
                $requestClientOptions['cookies'] = false;
            }
            if (isset($this->clientOptions['https'])) {
                $requestClientOptions['verify'] = $this->clientOptions['https'];
            }
            if (isset($this->clientOptions['user_agent'])) {
                $requestClientOptions['headers']['User-Agent'] = $this->clientOptions['user_agent'];
            }
            if (isset($this->clientOptions['custom_client_options'])) {
                $requestClientOptions = $this->clientOptions['custom_client_options'];
            }
            $this->guzzleClient = new Client($requestClientOptions);
        }
    }

    /**
     * @return array
     */
    public function setRedirectOptions(): array
    {
        $redirectOptions = [
            'strict' => true,
            'referer' => true,
            'protocols' => ['http', 'https'],
            'track_redirects' => true
        ];
        if (isset($this->clientOptions['redirect_num'])) {
            $redirectOptions['max'] = $this->clientOptions['redirect_num'];
        }
        return $redirectOptions;
    }

    public function setResponse(string $url, ResponseInterface $response): PResponse
    {
        $this->res->setRequestUrl($url);
        $this->res->setBody($response->getBody()->getContents());
        $this->res->setHttpCode($response->getStatusCode());
        $this->res->setLastUrl($response->getHeader('X-Guzzle-Redirect-History')[0] ?? $url);
        $this->res->setResponseHeaders($response->getHeaders());
        return $this->res;
    }

    public function post(string $url, array $clientOptions = []): PResponse
    {
        $this->getGuzzleClient();
        $response = $this->guzzleClient->request('POST', $url);
        return $this->setResponse($url, $response);
        // TODO: Implement post() method.
    }

    public function getFile(string $url, array $options = []): PResponse
    {
        if (empty($options) || empty($options['file_path'])) {
            throw new InvalidArgumentException ('No file_path provided');
        }
        $fp = fopen($options['file_path'], 'w+');
        if (!$fp) {
            throw new InvalidArgumentException ('filepath is not writable');
        }
        // TODO 
    }
}