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
    public $body; 
    public $responseHeaders;
    public $responseCode;
    public $error;
    /**
     * @var mixed
     */
    public $httpCode;
    public $url;
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

}