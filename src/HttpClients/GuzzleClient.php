<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;

class GuzzleClient implements InterfaceHttpClient
{
    public Client $client;

    public function __construct($opts = [])
    {
        $this->client = new Client($opts);
    }


}