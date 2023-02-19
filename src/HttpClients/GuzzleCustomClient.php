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

    public function setCustomOptions(array $customClientOptions): void
    {
        unset($this->guzzleClient);
        $this->clientOptions['custom_client_options'] = $customClientOptions;
    }

}