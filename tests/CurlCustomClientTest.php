<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class CurlCustomClientTest extends TestCase
{


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

    public function testDefaultClientOptions()
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
    public array $clientOptions = [
        CURLOPT_HEADER => 1,
        CURLOPT_NOBODY => 0,
        CURLOPT_USERAGENT => 'only head',
    ];
}