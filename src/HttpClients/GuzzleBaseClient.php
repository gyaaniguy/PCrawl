<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Gyaaniguy\PCrawl\Helpers\RegexStuff;
use Gyaaniguy\PCrawl\Response\Response;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;

class GuzzleBaseClient extends AbstractHttpClient
{
    protected Client $baseClient;

    public function __construct()
    {
        $this->res = new Response();
    }

    /**
     *
     * @param string $url
     * @param array $requestOptions
     * @return Response
     * @throws Exception
     */
    public function get(string $url): Response
    {
        $this->getBaseClient();
        try {
            $response = $this->baseClient->request('GET', $url);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
        return $this->setResponse($url, $response);
    }

    /**
     * Creates a guzzle client if required.
     * @return void
     */
    private function getBaseClient()
    {
        if (!isset($this->baseClient)) {
            $requestClientOptions = [];
            $requestClientOptions['allow_redirects'] = $this->setRedirectOptions();
            if (isset($this->clientOptions['connect_timeout'])) {
                $requestClientOptions['connect_timeout'] = $this->clientOptions['connect_timeout'];
            }
            if (isset($this->clientOptions['timeout'])) {
                $requestClientOptions['timeout'] = $this->clientOptions['timeout'];
            }
            if (isset($this->clientOptions['headers'])) {
                $requestClientOptions['headers'] = RegexStuff::headerToAssoc($this->clientOptions['headers']);
            }
            if (!empty($this->clientOptions['cookies'])) {
                $requestClientOptions['cookies'] = $this->clientOptions['cookies'];
            }
            if (isset($this->clientOptions['user_agent'])) {
                $requestClientOptions['headers']['User-Agent'] = $this->clientOptions['user_agent'];
            }
            if (isset($this->clientOptions['proxyPort'])) {
                $requestClientOptions['proxy'] = $this->getProxyStr();
            }
            if (isset($this->clientOptions['custom_client_options'])) {
                $requestClientOptions = $this->clientOptions['custom_client_options'];
            }
            $this->baseClient = new Client($requestClientOptions);
        }
    }

    /**
     * handles setting the redirection object for guzzleclient.
     * @return array
     */
    private function setRedirectOptions(): array
    {
        $redirectOptions = [
            'strict' => true,
            'referer' => true,
            'protocols' => ['http', 'https'],
            'track_redirects' => true
        ];
        if (isset($this->clientOptions['redirect_num'])) {
            $redirectOptions['max'] = $this->clientOptions['redirect_num'];
            $redirectOptions['track_redirects'] = true;
            if ($this->clientOptions['redirect_num'] === 0) {
                $redirectOptions['track_redirects'] = false;
            }
        }
        return $redirectOptions;
    }

    /**
     * Sets the library response object, and its various fields from the values of the guzzles response.
     * @param string $url
     * @param ResponseInterface $response
     * @return Response
     */
    public function setResponse(string $url, ResponseInterface $response): Response
    {
        $this->res->setRequestUrl($url);
        $this->res->setBody($response->getBody()->getContents());
        $this->res->setHttpCode($response->getStatusCode());
        $this->res->setLastUrl($response->getHeader('X-Guzzle-Redirect-History')[0] ?? $url);
        $this->res->setResponseHeaders($response->getHeaders());
        return $this->res;
    }

    /**
     * @param string $url
     * @param $postData
     * @return Response
     * @throws Exception
     */
    public function post(string $url, $postData): Response
    {
        $this->getBaseClient();
        try {
            $response = $this->baseClient->request('POST', $url, ['body' => $postData]);
        } catch (GuzzleException $e) {
            throw new Exception($e->getMessage());
        }
        return $this->setResponse($url, $response);
    }

    public function getFile(string $url, array $options = []): Response
    {
        if (empty($options) || empty($options['file_path'])) {
            throw new InvalidArgumentException('No file_path provided');
        }
        $this->getBaseClient();
        $response = $this->baseClient->request('GET', $url, [
            'sink' => $options['file_path']
        ]);
        return $this->setResponse($url, $response);
    }

    /**
     * @return string
     */
    public function getProxyStr(): string
    {
        $proxyStr = '';
        if (!empty($this->clientOptions['proxyAuth'])) {
            $proxyStr .= $this->clientOptions['proxyAuth'] . '@';
        }
        $proxyStr .= $this->clientOptions['proxyPort'];
        return $proxyStr;
    }
}
