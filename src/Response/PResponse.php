<?php

namespace Gyaaniguy\PCrawl\Response;

use Gyaaniguy\PCrawl\Parsing\PParser;

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

    /**
     * @return string
     */
    public function getBody(): string
    {
        if ($this->body === null) {
            throw new \InvalidArgumentException('PResponse::getBody() requires a body to be set.');
        }
        return $this->body;
    }

    /**
     * @param mixed $body
     */
    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    /**
     * @param mixed $requestUrl
     */
    public function setRequestUrl(string $requestUrl): void
    {
        $this->requestUrl = $requestUrl;
    }

    /**
     * @return string
     */
    public function getHttpCode(): int
    {
        return $this->httpCode;
    }

    /**
     * @param mixed $httpCode
     */
    public function setHttpCode(int $httpCode): void
    {
        $this->httpCode = $httpCode;
    }

    /**
     * @return array
     */
    public function getResponseHeaders(): array
    {
        return $this->responseHeaders;
    }

    /**
     * @param array $responseHeaders
     */
    public function setResponseHeaders(array $responseHeaders): void
    {
        $this->responseHeaders = $responseHeaders;
    }

    /**
     * @return string
     */
    public function getError(): string
    {
        return $this->error;
    }

    /**
     * @param mixed $error
     */
    public function setError(string $error): void
    {
        $this->error = $error;
    }

    /**
     * @return string
     */
    public function getLastUrl(): string
    {
        return $this->lastUrl;
    }

    public function setLastUrl(string $lastUrl)
    {
        $this->lastUrl = $lastUrl;
    }
}
