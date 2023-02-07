<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Response\PResponse;

class GuzzleClient implements InterfaceHttpClient
{
    public Client $guzzleClient;
    private PResponse $res;


    public function __construct()
    {
        $this->res = new PResponse();
    }

    function getGuzzleClient($clientOptions)
    {
        if (!$this->guzzleClient) {
            $clientOptionsGuzzle = [];
            if (isset($clientOptions['headers'])) {
                $clientOptionsGuzzle['headers'] = $clientOptions['headers'];
            }
            if (!empty($clientOptions['enable_cookies'])) {
                $clientOptionsGuzzle['cookies'] = true;
            }
            if (!empty($clientOptions['disable_cookies'])) {
                $clientOptionsGuzzle['cookies'] = false;
            }
            if (isset($clientOptions['https'])) {
                $clientOptionsGuzzle['verify'] = $clientOptions['https'];
            }
            if (isset($clientOptions['user_agent'])) {
                $clientOptionsGuzzle['headers']['User-Agent'] = $clientOptions['user_agent'];
            }
            $redirectOptions = [
                'strict'    => true,
                'referer'   => true,
                'protocols' => ['http', 'https'],
                'track_redirects' => true
            ];
            if (isset($clientOptions['redirect_num'])) {
                $redirectOptions['max'] = $clientOptions['redirect_num'];
            }
            $clientOptionsGuzzle['allow_redirects'] = $redirectOptions;

            if (!empty($clientOptions['custom_client_options']) ){
                $clientOptionsGuzzle = array_merge($clientOptionsGuzzle, $clientOptions['custom_client_options']);
            }

            $this->guzzleClient = new Client($clientOptionsGuzzle);
        }
    }

    public function get(string $url, array $clientOptions = []): PResponse
    {
        $this->getGuzzleClient($clientOptions);
        $response = $this->guzzleClient->request('GET', $url);
        
        $this->res->setRequestUrl($url);
        $this->res->setBody($response->getBody());
        $this->res->setError($response->getReasonPhrase());
        $this->res->setHttpCode($response->getStatusCode());
        $this->res->setLastUrl($response->);
        $this->res->setResponseHeaders($response->getHeaders());
        return $this->res;
    }

    public function post(string $url, array $options = []): PResponse
    {
        // TODO: Implement post() method.
    }


    public function setHeaders(array $headers)
    {
        unset($this->guzzleClient);
    }

    public function enableCookies(string $cookiePath)
    {
        unset($this->guzzleClient);
    }

    public function disableCookies()
    {
        unset($this->guzzleClient);
    }

    public function allowHttps()
    {
        unset($this->guzzleClient);
    }

    public function setRedirects(int $num)
    {
        unset($this->guzzleClient);
    }

    public function setUserAgent(string $userAgent)
    {
        unset($this->guzzleClient);
    }

}