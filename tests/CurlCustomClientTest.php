<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class CurlCustomClientTest extends TestCase
{

    public function testCustomClientRaw()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_USERAGENT, "raw curl");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $curlCustom = new CurlCustomClient();
        $curlCustom->setRawClient($ch);

        $req = new Request($curlCustom);

        $res = $req->get('https://manytools.org/http-html-text/http-request-headers');
        self::assertStringContainsStringIgnoringCase("raw curl", $res->getBody());
    }
    public function testSetCustomClientOptions()
    {
        $req = new Request();
        $client = new CurlCustomClient();
        $client->setCustomOptions([
            CURLOPT_HEADER => 1,
            CURLOPT_NOBODY => 1,
        ]);
        $req->setClient($client);
        $onlyHeadRes = $req->get('icanhazip.com');
        self::assertStringContainsString("HTTP", $onlyHeadRes->getBody());
        self::assertStringNotContainsString("html", $onlyHeadRes->getBody());
    }

    public function testDefaultCustomClientOptions()
    {
        $req = new Request();
        $req->setClient(new OnlyHeadClient());
        $onlyHeadRes = $req->get('icanhazip.com');

        self::assertStringContainsString("HTTP", $onlyHeadRes->getBody());
        self::assertStringNotContainsString("html", $onlyHeadRes->getBody());
    }

}


class OnlyHeadClient extends CurlCustomClient
{
    public array $customClientOptions = [
        CURLOPT_HEADER => 1,
        CURLOPT_NOBODY => 0,
        CURLOPT_USERAGENT => 'only head',
    ];
}