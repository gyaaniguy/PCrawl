<?php

namespace Gyaaniguy\PCrawl\HttpClients;

class CurlCustomClient extends CurlBaseClient
{
    public function __construct()
    {
        parent::__construct();
        if (!empty($this->clientOptions)) {
            $this->setCustomOptions($this->clientOptions);
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
        $this->clientOptions['custom_client_options'] = $customClientOptions;
    }
}