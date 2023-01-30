<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Brobot;
use Gyaaniguy\PCrawl\OnlyHeadClient;
use Gyaaniguy\PCrawl\PRequest;
use PHPUnit\Framework\TestCase;

class CurlClientTest extends TestCase
{

    public function testGet()
    {
        $req = new PRequest();
        $res = $req->get('http://www.whatsmyua.info/');
        self::assertIsInt( $res->getHttpCode());
        self::assertEquals(301, $res->getHttpCode());
        self::assertEquals('http://www.whatsmyua.info/', $res->getLastUrl());
        self::assertIsArray( $res->getResponseHeaders());
        self::assertArrayHasKey('location',$res->getResponseHeaders());
        self::assertArrayHasKey(0,$res->getResponseHeaders()['location']);
        self::assertRegExp('/whatsmyua/',$res->getResponseHeaders()['location'][0]);
    }

    public function testErrors()
    {
        $req = new PRequest();
        $res = $req->get('fake.notExiststhisthingshere');
        $errors = $res->getError();
        $this->assertNotEmpty($errors);
        $this->assertStringContainsString('not resolve',$errors);
    }
}
