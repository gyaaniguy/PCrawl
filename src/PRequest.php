<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\Helpers\RegexStuff;
use Gyaaniguy\PCrawl\HttpClients\InterfaceHttpClient;
use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\Response\PResponse;

/**
 *  A sample class
 *
 * @author Nikhil Jain
 */
class PRequest
{
    private array $clientOptions = [
        'user_agent' => '',
        'headers' => [],
        'tidy' => false,
        'https' => false,
        'http_client' => '',
        'enable_cookies' => '',
        'redirect_num' => '',
        'custom_client_options' => [],
    ];
    private PResponse $lastRawResponse;
    private InterfaceHttpClient $httpClient;
    private string $cookiePath;

    public function __construct($options = [])
    {
        // httpClient block should be first, so custom request options override its defaultoptions   
        $this->setClientOptions($options);
    }

    public function getFile($url = '', array $options = []): PResponse
    {
        if (!empty($this->clientOptions['enable_cookies'])) {
            $this->enableCookies();
        }
        $this->clientOptions['file_path'] = $options['file_path'];
        $this->lastRawResponse = $this->httpClient->get($url, $this->clientOptions);
        return $this->lastRawResponse;
    }
    public function get($url = '', array $options = []): PResponse
    {
        if (!empty($this->clientOptions['enable_cookies'])) {
            $this->enableCookies();
        }

        $this->lastRawResponse = $this->httpClient->get($url, array_merge($this->clientOptions, $options));
        return $this->lastRawResponse;
    }

    public function post($url, $postData): PResponse
    {
        if ($this->clientOptions['enable_cookies']) {
            $this->enableCookies();
        }
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $this->lastRawResponse = $this->httpClient->post($url, $postData);
        return $this->lastRawResponse;
    }

    public function setUserAgent(string $userAgent): PRequest
    {
        $this->clientOptions['user_agent'] = $userAgent;
        $this->httpClient->setUserAgent($userAgent);
        return $this;
    }

    public function addRequestHeaders(array $headers): PRequest
    {
        if (!empty($headers)) {
            $headersAssoc = RegexStuff::headerToAssoc($headers);
            $OriginalHeadersAssoc = RegexStuff::headerToAssoc($this->clientOptions['headers']);
            $allHeaders = array_merge($OriginalHeadersAssoc, $headersAssoc);
            if (!empty($allHeaders)) {
                array_walk($allHeaders, function (&$val, $key) {
                    $val = $key . ': ' . $val;
                });
                $this->clientOptions['headers'] = array_values($allHeaders);
                $this->httpClient->setHeaders($this->clientOptions['headers']);
            }
        }
        return $this;
    }

    public function setRequestHeaders(array $headers): PRequest
    {
        $this->clientOptions['headers'] = $headers;
        $this->httpClient->setHeaders($headers);
        return $this;
    }

    public function enableCookies(): PRequest
    {
        $this->clientOptions['enable_cookies'] = true;
        if (empty($this->cookiePath)) {
            $this->cookiePath = '/tmp/cook-prequest-' . uniqid();
        }
        $this->httpClient->enableCookies($this->cookiePath);
        return $this;
    }

    public function disableCookies(): PRequest
    {
        $this->clientOptions['enable_cookies'] = false;
        $this->httpClient->disableCookies();
        return $this;
    }

    public function clearCookies(): PRequest
    {
        if (!empty($this->cookiePath)) {
            unlink($this->cookiePath);
        }
        return $this;
    }

    public function allowHttps(): PRequest
    {
        $this->clientOptions['https'] = true;
        $this->httpClient->allowHttps();
        return $this;
    }

    public function setRedirects($num = 2): PRequest
    {
        $this->clientOptions['redirect_num'] = intval($num, 10);
        $this->httpClient->setRedirects($this->clientOptions['redirect_num']);
        return $this;
    }

    public function useCurl(): PRequest
    {
        $this->setClient(new CurlClient());
        return $this;
    }

    public function useGuzzle(): PRequest
    {
        $this->setClient(new CurlClient());
        return $this;
    }


    /**
     * @return array
     */
    public function getClientOptions(): array
    {
        return $this->clientOptions;
    }


    public function setClient($client): PRequest
    {
        $this->clientOptions['http_client'] = $client;
        $this->httpClient = $client;
        $this->setClientDefaultOptions($client);

        return $this;
    }


    // Options that are going to be relevant only for different clients - so different options for curl and guzzle
    public function setCustomClientOptions($customOpts): PRequest
    {
        $this->clientOptions['custom_client_options'] = $customOpts;
        if (method_exists($this->httpClient, 'setCustomClientOptions')) {
            $this->httpClient->setCustomClientOptions($customOpts);
        }
        return $this;
    }

    /**
     * @return InterfaceHttpClient
     */
    public function getHttpClient(): InterfaceHttpClient
    {
        return $this->httpClient;
    }

    /**
     * @return string
     */
    public function getCookiePath(): string
    {
        return $this->cookiePath;
    }

    public function closeConnection()
    {
        if (!empty($this->clientOptions['http_client']) && method_exists($this->clientOptions['http_client'], 'close')) {
            $this->clientOptions['http_client']->close();
        }
    }

    public function setClientDefaultOptions($client): void
    {
        $allOptions = $options = $this->getClientOptions();
        if (!empty($client->defaultOptions) && is_array($client->defaultOptions)) {
            $allOptions = array_merge($options, $client->defaultOptions);
        }
        if (!empty($allOptions)) {
            foreach ($allOptions as $optionName => $optionValue) {
                if (!empty($optionName)) {
                    switch ($optionName) {
                        case 'user_agent':
                            $this->setUserAgent($optionValue);
                            break;
                        case 'headers':
                            $this->setRequestHeaders($optionValue);
                            break;
                        case 'redirect_num':
                            $this->setRedirects($optionValue);
                            break;
                        case 'https':
                            $this->allowHttps();
                            break;
                        case 'custom_client_options' && is_array($optionValue):
                            $this->setCustomClientOptions($optionValue);
                            break;
                    }
                }
            }
        }
    }

    public function branch(): PRequest
    {
        $clone = clone $this;
        return $clone;
    }

    public function __clone()
    {
        foreach (get_object_vars($this) as $name => $value) {
            if (is_object($value)) {
                $this->{$name} = clone $value;
            }
        }
    }

    /**
     * @param $clientOptions
     * @return PRequest
     */
    public function setClientOptions($clientOptions): PRequest
    {
        if (!empty($clientOptions['http_client']) && is_object($clientOptions['http_client'])) {
            $this->setClient($clientOptions['http_client']);
        } else {
            $this->useCurl();
        }
        if (!empty($clientOptions['user_agent'])) {
            $this->setUserAgent($clientOptions['user_agent']);
        }
        if (!empty($clientOptions['headers'])) {
            $this->setRequestHeaders($clientOptions['headers']);
        }
        if (!empty($clientOptions['https'])) {
            $this->allowHttps();
        }
        if (!empty($clientOptions['enable_cookies'])) {
            $this->enableCookies();
        }
        if (!empty($clientOptions['redirect_num'])) {
            $this->setRedirects($clientOptions['redirect_num']);
        }
        if (!empty($clientOptions['custom_client_options'])) {
            $this->setCustomClientOptions($clientOptions['custom_client_options']);
        }
        return $this;
    }

}