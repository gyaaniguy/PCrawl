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
    private array $options = [
        'user_agent' => '',
        'headers' => [],
        'tidy' => false,
        'https' => false,
        'httpclient' => 'curl',
        'enable_cookies' => '',
        'redirect_num' => '',
        'custom_client_options' => [],
    ];
    private PResponse $lastRawResponse;
    private InterfaceHttpClient $httpClient;
    private string $cookiePath;

    public function __construct($options = [])
    {
        if (!empty($options['httpClient']) && $options['httpClient'] == 'guzzle') {
            $this->useGuzzle();
        } else {
            $this->useCurl();
        }
        if (!empty($options['user_agent'])) {
            $this->setUserAgent($options['user_agent']);
        }
        if (!empty($options['headers'])) {
            $this->setRequestHeaders($options['headers']);
        }
        if (!empty($options['tidy'])) {
            $this->enableTidy();
        }
        if (!empty($options['https'])) {
            $this->allowHttps();
        }
        if (!empty($options['enable_cookies'])) {
            $this->enableCookies();
        }
        if (!empty($options['redirect_num'])) {
            $this->setRedirects($options['redirect_num']);
        }
        if (!empty($options['custom_client_options'])) {
            $this->setCustomClientOptions($options['custom_client_options']);
        }
    }

    public function get($url = ''): PResponse
    {
        if (!empty($this->options['enable_cookies'])) {
            $this->enableCookies();
        }

        $this->lastRawResponse = $this->httpClient->get($url);
        return $this->lastRawResponse;
    }

    public function post($url, $postData): PResponse
    {
        if ($this->options['enable_cookies']) {
            $this->enableCookies();
        }
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $this->lastRawResponse = $this->httpClient->post($url, $postData);
        $this->postProcessing($this->lastRawResponse);
        return $this->lastRawResponse;
    }

    public function setUserAgent(string $userAgent): PRequest
    {
        $this->options['user_agent'] = $userAgent;
        $this->httpClient->setUserAgent($userAgent);
        return $this;
    }

    public function addRequestHeaders(array $headers): PRequest
    {
        if (!empty($headers)) {
            $headersAssoc = RegexStuff::headerToAssoc($headers);
            $OriginalHeadersAssoc = RegexStuff::headerToAssoc($this->options['headers']);
            $allHeaders = array_merge($OriginalHeadersAssoc, $headersAssoc);
            if (!empty($allHeaders)) {
                array_walk($allHeaders, function (&$val, $key) {
                    $val = $key . ': ' . $val;
                });
                $this->options['headers'] = array_values($allHeaders);
                $this->httpClient->setHeaders($this->options['headers']);
            }
        }
        return $this;
    }

    public function setRequestHeaders(array $headers): PRequest
    {
        $this->options['headers'] = $headers;
        $this->httpClient->setHeaders($headers);
        return $this;
    }

    public function enableCookies(): PRequest
    {
        $this->options['enable_cookies'] = true;
        if (empty($this->cookiePath)) {
            $this->cookiePath = '/tmp/cook-prequest-' . uniqid();
        }
        $this->httpClient->enableCookies($this->cookiePath);
        return $this;
    }

    public function disableCookies(): PRequest
    {
        $this->options['enable_cookies'] = false;
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
        $this->options['https'] = true;
        $this->httpClient->allowHttps();
        return $this;
    }

    public function setRedirects(int $num = 2): PRequest
    {
        $this->options['redirect_num'] = $num;
        $this->httpClient->setRedirects($num);
        return $this;
    }

    public function useCurl(): PRequest
    {
        $this->httpClient = new CurlClient();
        return $this;
    }

    public function useGuzzle(): PRequest
    {
        return $this;
    }

    public function enableTidy(): PRequest
    {
        $this->options['tidy'] = true;
        return $this;
    }


    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }


    public function setClient($client): PRequest
    {
        $this->httpClient = $client;
        if (!empty($client->defaultOptions) && is_array($client->defaultOptions)) {
            foreach ($client->defaultOptions as $optionName => $optionValue) {
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
                        case 'client' && is_string($optionValue):
                            $this->setClient($optionValue);
                            break;
                    }
                }
            }
        }
        return $this;
    }


    public function setCustomClientOptions($customOpts): PRequest
    {
        $this->options['custom_client_options'] = $customOpts;
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
        if (!empty($this->options['httpclient']) && $this->options['httpclient'] === 'curl') {
            $this->httpClient->close();
        }
    }
}