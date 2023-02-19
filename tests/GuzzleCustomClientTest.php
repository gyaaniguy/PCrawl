<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\PRequest;
use PHPUnit\Framework\TestCase;

class GuzzleCustomClientTest extends TestCase
{    public function testSetRedirects()
    {
        $guzzleClientOptions = [
            'headers' => [
                'head5' => 'value' ,
            ],
        ];
        $client = new GuzzleCustomClient();
        $client->setCustomOptions($guzzleClientOptions);
        
        $req = new PRequest($client);
        $res = $req->get('https://manytools.org/http-html-text/http-request-headers/');
        self::assertStringContainsString("Head5", $res->getBody());

    }
    public function testDefaultClientOptions()
    {
        $req = new PRequest();
        $req->setClient(new OnlyHeadGuzzleClient());
        $onlyHeadRes = $req->get('https://manytools.org/http-html-text/http-request-headers');
        self::assertStringContainsString("Head5", $onlyHeadRes->getBody());

    }
}


class OnlyHeadGuzzleClient extends GuzzleCustomClient
{
    public array $customClientOptions = [
        'headers' => [
            'head5' => 'value' ,
        ],
    ];
}
