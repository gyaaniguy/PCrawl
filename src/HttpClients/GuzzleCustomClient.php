<?php

namespace Gyaaniguy\PCrawl\HttpClients;

class GuzzleCustomClient extends GuzzleBaseClient
{

    public function __construct()
    {
        parent::__construct();
        if (!empty($this->clientOptions)) {
            $this->setCustomOptions($this->clientOptions);
        }
    }

    /**
     * The param is passed as is to the guzzle client constructor. This allows setting any guzzle option not present in the library's GuzzleClient class.
     * @param array $customClientOptions
     * @return void
     */
    public function setCustomOptions(array $customClientOptions): void
    {
        unset($this->baseClient);
        $this->clientOptions['custom_client_options'] = $customClientOptions;
    }

    public function setRawClient(\GuzzleHttp\Client $client): void
    {
        $this->baseClient = $client;
    }

}