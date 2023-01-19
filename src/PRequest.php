<?php namespace Gyaaniguy\PCrawl;

/**
*  A sample class
*
*  @author Nikhil Jain
*/
class PRequest{

    private array $options = [
        'user_agent'      => '',
        'headers'         => [],
        'tidy'            => false,
        'https'           => false,
        'httpclient'      => 'curl',
        'cookies_enabled' => '',
        'redirects'       => '',
    ]; 
    private PResponse $lastRawResponse;
    private HttpClient $httpClient;
    private string $cookiePath;
//    private string $hostName;

    public function __construct($options)
    {
        if (!empty($options['httpClient']) && $options['httpClient'] == 'guzzle') {
            $this->useGuzzle();
        } else {
            $this->useCurl();
        }        
        if (!empty($options['user_agent'])) {
            $this->setUserAgent($options['user_agent']);
        } 
        if (!empty($options['headers']) ) {
            $this->setHeaders($options['headers']);
        } 
        if (!empty($options['tidy'])) {
            $this->enableTidy();
        }
        if (!empty($options['https'])) {
            $this->setStrictHttps();
        }
        if (!empty($options['cookies_enabled'])) {
            $this->enableCookies();
        } 
        if (!empty($options['redirects']) ) {
            $this->setRedirects($options['redirects']);
        }         
    }


    function get($url = '') : PResponse {
        if (!empty($this->options['cookies_enabled'])) {
            $this->enableCookies();
        }
        
        $this->lastRawResponse = $this->httpClient->get($url);
        $this->postProcessing($this->lastRawResponse);
        return $this->lastRawResponse;
    }
    
    function post($url,$postData) : PResponse {
        if ($this->options['cookies_enabled']) {
            $this->enableCookies();
        }
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $this->lastRawResponse = $this->httpClient->post($url,$postData);
        $this->postProcessing($this->lastRawResponse);
        return $this->lastRawResponse;    }

    function setUserAgent(string $userAgent) : PRequest
    {
        $this->options['user_agent'] = $userAgent;
        $this->httpClient->setUserAgent($userAgent);
        return $this;
    }

    function setHeaders(array $headers): PRequest
    {
        $this->options['headers'] = $headers;
        $this->httpClient->setHeaders($headers);
        return $this;
    }

    function enableCookies(): PRequest
    {
        $this->options['cookies_enabled'] = true;
        if (empty($this->cookiePath)) {
            $this->cookiePath = '/tmp/cook-prequest-'.uniqid();
        }
        $this->httpClient->enableCookies($this->cookiePath);
        return $this;
    }
    
    function disableCookies(): PRequest
    {
        $this->options['cookies_enabled'] = false;
        $this->httpClient->disableCookies();
        return $this;
    }
    
    function clearCookies(): PRequest
    {
        $this->httpClient->clearCookies();
        return $this;
    }
    function setStrictHttps(): PRequest
    {
        $this->options['https'] = true;
        $this->httpClient->allowHttps();
        return $this;
    }
    function setRedirects(int $num = 2): PRequest
    {
        $this->options['redirects'] = $num;
        $this->httpClient->setRedirects($num);
        return $this;
    }
    function useCurl(): PRequest
    {
        $this->httpClient = new CurlClient();
        return $this;
    }

    function useGuzzle(): PRequest
    {
        return $this;
    }

    function enableTidy()
    {
        $this->options['tidy'] = true;
    }
    
    function postProcessing(PResponse $res)
    {
        if ($this->options['tidy']) {
            $res->tidy();
        }
    }

    /**
     * @return array
     */
    public function getOptions(): array
    {
        return $this->options;
    }

}