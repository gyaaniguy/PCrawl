<?php

namespace Gyaaniguy\PCrawl;

use Exception;
use GuzzleHttp\Exception\GuzzleException;
use Gyaaniguy\PCrawl\Helpers\RegexStuff;
use Gyaaniguy\PCrawl\HttpClients\AbstractHttpClient;
use Gyaaniguy\PCrawl\HttpClients\GuzzleClient;
use Gyaaniguy\PCrawl\HttpClients\InterfaceHttpClient;
use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\Response\PResponse;
use InvalidArgumentException;

/**
 *  A sample class
 *
 * @author Nikhil Jain
 */
class PRequest
{
//    private PResponse $lastRawResponse;
    private AbstractHttpClient $httpClient;

    public function __construct(AbstractHttpClient $httpClient = null)
    {
        if ($httpClient === null) {
            $this->httpClient = new GuzzleClient();
            return;
        }
        $this->httpClient = $httpClient;
    }

    /**
     * @throws Exception
     */
    public function getFile($url = '', array $options = []): PResponse
    {
        if (method_exists($this->httpClient, 'getFile') === false) {
            throw new InvalidArgumentException('getFile() method not implemented in ' . get_class($this->httpClient));
        }
        if (!isset($options['file_path'])) {
            throw new InvalidArgumentException('file_path not set in options');
        }
        $fileOptions['file_path'] = $options['file_path'];
        $this->lastRawResponse = $this->httpClient->getFile($url, $fileOptions);
        return $this->lastRawResponse;
    }

    public function get($url = '', $requestOptions = []): PResponse
    {
        try {
            $this->lastRawResponse = $this->httpClient->get($url, $requestOptions = []);
        } catch (GuzzleException $e) {
            $this->lastRawResponse = new PResponse();
            $this->lastRawResponse->setError($e->getMessage());
        }
        return $this->lastRawResponse;
    }

    public function post($url, $postData): PResponse
    {
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $this->lastRawResponse = $this->httpClient->post($url, $postData);
        return $this->lastRawResponse;
    }



//    public function enableCookies(): PRequest
//    {
//        $this->clientOptions['enable_cookies'] = true;
//        if (empty($this->cookiePath)) {
//            $this->cookiePath = '/tmp/cook-prequest-' . uniqid();
//        }
//        if (method_exists($this->httpClient,'enableCookies')) {
//            $this->httpClient->enableCookies($this->cookiePath);
//        }
//        return $this;
//    }

//    public function disableCookies(): PRequest
//    {
//        $this->clientOptions['enable_cookies'] = false;
//        if (method_exists($this->httpClient,'disableCookies')) {
//            $this->httpClient->disableCookies();
//        }
//        return $this;
//    }
//
//    public function clearCookies(): PRequest
//    {
//        if (!empty($this->cookiePath)) {
//            unlink($this->cookiePath);
//        }
//        return $this;
//    }


    public function setClient($client): PRequest
    {
        $this->httpClient = $client;
        return $this;
    }

    /**
     * @return InterfaceHttpClient
     */
    public function getClient(): AbstractHttpClient
    {
        return $this->httpClient;
    }

    // branch clone Only needed if storing lastresponse in request obj. So remvoing to reduce complexity. Maybe will have a subclass for that purpose. Base class will be clean, lean.
//    public function branch(): PRequest
//    {
//        return clone $this;
//    }

//    public function __clone()
//    {
//        foreach (get_object_vars($this) as $name => $value) {
//            if (is_object($value)) {
//                $this->{$name} = clone $value;
//            }
//        }
//    }

}