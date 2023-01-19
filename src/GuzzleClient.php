<?php

namespace Gyaaniguy\PCrawl;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Psr\Http\Message\ResponseInterface;

class GuzzleClient extends HttpClient
{
    public Client $client ;

    public function __construct($opts = [])
    {
        $this->client = new Client($opts);
    }

    

}