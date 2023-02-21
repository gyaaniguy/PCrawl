<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\PRequest;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{

    public function testGet()
    {
        $req = new PRequest();
        $res = $req->get('http://www.whatsmyua.info/');
        self::assertIsInt($res->getHttpCode());
        self::assertEquals(200, $res->getHttpCode());
        self::assertEquals('https://www.whatsmyua.info/', $res->getLastUrl());
        self::assertIsArray($res->getResponseHeaders());
        self::assertArrayHasKey('Server', $res->getResponseHeaders());
        self::assertArrayHasKey(0, $res->getResponseHeaders()['Server']);
        self::assertRegExp('/nginx/', $res->getResponseHeaders()['Server'][0]);
    }

    public function testErrors()
    {
        $req = new PRequest();
        $res = $req->get('fake.notExiststhisthingshere');
        $errors = $res->getError();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('not resolve', $errors);
    }


    public function testSetRedirects()
    {
        $req = new PRequest();
        $client = new CurlClient();
        $client->setRedirects(4);
        $req->setClient($client);
        $res = $req->get('http://whatsmyua.info/');
        self::assertStringContainsStringIgnoringCase("user agent", $res->getBody());

        $req = new PRequest();
        $client = new CurlClient();
        $client->setRedirects(4);
        $req->setClient($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('redirect_num', $clientOptions);
        self::assertEquals(4, $clientOptions['redirect_num']);
        $client->setUserAgent("after change");
        $res = $req->get('http://whatsmyua.info/');
        self::assertStringContainsStringIgnoringCase("after change", $res->getBody());
        self::assertEquals(200, $res->getHttpCode());


        $client->setRedirects(0);
        $res = $req->get('http://whatsmyua.info/');
        self::assertEquals(301, $res->getHttpCode());

    }
}
