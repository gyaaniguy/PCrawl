<?php namespace Gyaaniguy\PCrawl;

/**
*  A sample class
*
*  @author Nikhil Jain
*/
class PRequest{

    public $tidyEnabled = false;
    public $cookiesEnables = false;
    private PResponse $lastRawResponse;
    private HttpClient $httpClient;
    private string $cookiePath;
    private string $hostName;

    function get($url = '') : PResponse {
        if ($this->cookiesEnables) {
            $this->enableCookies();
        }
        $this->lastRawResponse = $this->httpClient->get($url);
        return $this->postProcessing($this->lastRawResponse, $url);
    }
    
    function post($url = '',$postData) : PResponse {
        if ($this->cookiesEnables) {
            $this->enableCookies();
        }
        if (is_array($postData)) {
            $postData = http_build_query($postData);
        }
        $this->lastRawResponse = $this->httpClient->post($url,$postData);
        return $this->postProcessing($this->lastRawResponse, $url);
    }

    function setUserAgent(string $userAgent)
    {
        $this->httpClient->setUserAgent($userAgent);
        return $this;
    }

    function setHeaders(array $headers){
        $this->httpClient->setHeaders($headers);
        return $this;
    }

    function enableCookies(){
        $this->cookiesEnables = true;
        if (empty($this->cookiePath)) {
            $this->cookiePath = '/tmp/cook-prequest-'.uniqid();
        }
        $this->httpClient->enableCookies($this->cookiePath);
        return $this;
    }
    function clearCookies(){
        $this->httpClient->clearCookies();
        return $this;
    }
    function setStrictHttps(){
        $this->httpClient->allowHttps();
        return $this;
    }
    function setRedirects(int $num = 2)
    {
        $this->httpClient->setRedirects($num);
        return $this;
    }    
    
    function useCurl()
    {
        $this->httpClient = new CurlClient();
        return $this;
    }
    function useGuzzle()
    {
        return $this;
    }

    public function enableTidy()
    {
        $this->tidyEnabled = true;
    }

    
    function postProcessing(PResponse $res, string $url): PResponse
    {
        if ($this->tidyEnabled) {
            $config = array(
                'indent'         => true,
                'output-xhtml'   => true,
                'wrap'           => 200);
            $tidy = new Tidy();
            $tidy->parseString($res->body, $config, 'utf8');
            $tidy->cleanRepair();
            $res->body = $tidy->value;
        }
        $res->url = $url ;
        $res->createParser()
        return $res;
    }

}