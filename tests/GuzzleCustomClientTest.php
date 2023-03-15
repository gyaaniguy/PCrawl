<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use GuzzleHttp\Client;
use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class GuzzleCustomClientTest extends TestCase
{
    public function testCustomClientRaw()
    {
        $myGuzzle = new Client(['base_uri' => 'https://manytools.org/', 'headers' => ['User-Agent' => "raw guzzle"]]);

        $guzzleCustom = new GuzzleCustomClient();
        $guzzleCustom->setRawClient($myGuzzle);

        $req = new Request();
        $req->setClient($guzzleCustom);

        $res = $req->get('http-html-text/http-request-headers');
        self::assertStringContainsStringIgnoringCase("raw guzzle", $res->getBody());
    }
    public function testCustomClientCustomOptions()
    {
        $guzzleCustom = new GuzzleCustomClient();
        $guzzleCustom->setCustomOptions(['base_uri' => 'https://manytools.org/', 'headers' => ['User-Agent' => "raw guzzle"]]);

        $req = new Request();
        $req->setClient($guzzleCustom);

        $res = $req->get('http-html-text/http-request-headers');
        self::assertStringContainsStringIgnoringCase("raw guzzle", $res->getBody());
    }

    public function testDefaultClientOptions()
    {
        $req = new Request();
        $req->setClient(new OnlyHeadGuzzleClient());
        $onlyHeadRes = $req->get('https://manytools.org/http-html-text/http-request-headers');
        self::assertStringContainsStringIgnoringCase("Head5", $onlyHeadRes->getBody());
    }
}

class OnlyHeadGuzzleClient extends GuzzleCustomClient
{
    public array $clientOptions = [
        'headers' => [
            'head5: value',
        ],
    ];
}
