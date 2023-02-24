<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Request\PRequest;
use PHPUnit\Framework\TestCase;

class GuzzleCustomClientTest extends TestCase
{
    public function testSetRedirects()
    {
        $guzzleClientOptions = [
            'headers' => [
                'head5' => 'value',
            ],
        ];
        $client = new PGuzzleCustomClient();
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
    public function testCustomClientAdvanced()
    {
        $myGuzzle = new Client(['base_uri' => 'https://manytools.org/', 'headers' => ['User-Agent' => "raw guzzle"]]);
        
        $guzzleCustom = new PGuzzleCustomClient();
        $guzzleCustom->setRawClient($myGuzzle);

        $req = new PRequest();
        $req->setClient($guzzleCustom);
        
        $res = $req->get('http-html-text/http-request-headers');
        self::assertStringContainsStringIgnoringCase("raw guzzle", $res->getBody());
    }
}


class OnlyHeadGuzzleClient extends PGuzzleCustomClient
{
    public array $clientOptions = [
        'headers' => [
            'head5' => 'value',
        ],
    ];
}
