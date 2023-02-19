<?php

namespace Gyaaniguy\PCrawl\HttpClients;

class GuzzleCustomClient extends GuzzleBaseClient
{
    protected array $customClientOptions = [] ;

    public function __construct()
    {
        parent::__construct();
        if (!empty($this->customClientOptions)) {
            $this->setCustomOptions($this->customClientOptions);
        }
    }

    public function setCustomOptions(array $customClientOptions): void
    {
        unset($this->guzzleClient);
        $this->clientOptions['custom_client_options'] = $customClientOptions;
    }
    
}