<?php

namespace Gyaaniguy\PCrawl\HttpClients;

class CurlCustomClient extends CurlBaseClient
{
    protected array $customClientOptions = [];

    public function __construct()
    {
        parent::__construct();
        if (!empty($this->customClientOptions)) {
            $this->setCustomOptions($this->customClientOptions);
        }
    }

    /**
     * Passes the param to the curl_setopt_array function.
     * Allows setting any curl option not present in the library's CurlClient class.
     * @param array $customClientOptions
     * @return void
     */
    public function setCustomOptions(array $customClientOptions): void
    {
        $this->curlInitIf();
        curl_setopt_array($this->ch, $customClientOptions);
        $this->customClientOptions = $customClientOptions;
    }

    /**
     * Pass in a curl client to use instead of the default one.
     * @param $client
     * @return void
     */
    public function setRawClient($client): void
    {
        $this->ch = $client;
    }
}
