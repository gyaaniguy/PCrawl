<?php

namespace Gyaaniguy\PCrawl\Response;

use Gyaaniguy\PCrawl\PParser;

/**
 *  A sample class
 *
 *  Use this section to define what this class is doing, the PHPDocumentator will use this
 *  to automatically generate an API documentation using this information.
 *
 * @author yourname
 */
class PResponse
{
    protected string $body;
    protected array $responseHeaders;
    protected string $error;
    protected string $httpCode;
    protected string $lastUrl;
    protected string $requestUrl;
    private PParser $parser;
    private PResponseMods $PResponseMods;



    /**
     * @param mixed $body
     */
    public function setBody($body): void
    {
        $this->body = $body;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
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

    public function createParser()
    {
        $this->parser = new PParser($this->body);
    }


    public function setLastUrl($lastUrl)
    {
        $this->lastUrl = $lastUrl;
    }


}