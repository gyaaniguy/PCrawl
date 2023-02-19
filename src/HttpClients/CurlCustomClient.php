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

    public function setCustomOptions(array $customClientOptions): void
    {
        $this->curlInitIf();
        curl_setopt_array($this->ch, $customClientOptions);
        $this->clientOptions['custom_client_options'] = $customClientOptions;
    }
}