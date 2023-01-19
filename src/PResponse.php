<?php namespace Gyaaniguy\PCrawl;

/**
*  A sample class
*
*  Use this section to define what this class is doing, the PHPDocumentator will use this
*  to automatically generate an API documentation using this information.
*
*  @author yourname
*/
class PResponse{
    protected $body; 
    protected $responseHeaders;
    protected $error;
    protected $httpCode;
    protected $requestUrl;
    private PParser $parser;

    function modBody(array $callbacks ){
        foreach ($callbacks as $middleware) {
            $this->body = $middleware($this->body);          
        }        
    }

    function createParser()
    {
        $this->parser = new PParser($this->body);
    }

    // use url to convert
    function toAbsoluteUrls(){
        
    }

    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @param mixed $responseHeaders
     */
    public function setResponseHeaders($responseHeaders): void
    {
        $this->responseHeaders = $responseHeaders;
    }

    /**
     * @param mixed $error
     */
    public function setError($error): void
    {
        $this->error = $error;
    }

    /**
     * @param mixed $httpCode
     */
    public function setHttpCode($httpCode): void
    {
        $this->httpCode = $httpCode;
    }
    /**
     * @param mixed $requestUrl
     */
    public function setRequestUrl($requestUrl): void
    {
        $this->requestUrl = $requestUrl;
    }

    public function tidy()
    {
        $config = array(
            'indent'         => true,
            'output-xhtml'   => true,
            'wrap'           => 200);
        $tidy = new Tidy();
        $tidy->parseString($this->body, $config, 'utf8');
        $tidy->cleanRepair();
        $this->body = $tidy->value;
    }

}