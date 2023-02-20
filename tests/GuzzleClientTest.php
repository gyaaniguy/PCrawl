<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\PRequest;
use PHPUnit\Framework\TestCase;

class GuzzleClientTest extends TestCase
{
    public function testSetRedirects()
    {
        $req = new PRequest();
        $client = new GuzzleClient();
        $client->setRedirects(4);
        $req->setClient($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('redirect_num', $clientOptions);
        self::assertEquals(4, $clientOptions['redirect_num']);
    }

    public function testSetUserAgent()
    {
        $req = new PRequest();
        $client = new GuzzleClient();
        $userAgentStr = "user agent test";
        $client->setUserAgent($userAgentStr);
        $req->setClient($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('user_agent', $clientOptions);
        self::assertEquals($userAgentStr, $clientOptions['user_agent']);
        $res = $req->get('https://www.whatsmyua.info/');
        self::assertStringContainsString("user agent test", $res->getBody());
    }

    public function testSetHeaders()
    {
        $client = new GuzzleClient();
        $headers = [
            'head1: val 1',
            'head2: val 2',
        ];
        $client->setHeaders($headers);
        $req = new PRequest($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('headers', $clientOptions);
        self::assertEquals($headers, $clientOptions['headers']);
        $res = $req->get('https://manytools.org/http-html-text/http-request-headers/');
        self::assertStringContainsString("Head2", $res->getBody());
    }

    public function testAddHeaders()
    {
        $client = new GuzzleClient();
        $headers = [
            'head1: val 1',
        ];
        $client->setHeaders($headers);
        $req = new PRequest($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('headers', $clientOptions);
        self::assertEquals($headers, $clientOptions['headers']);
        $additionalHeaders = [
            'head2: val 2',
        ];
        $client->addHeaders($additionalHeaders);
        $res = $req->get('https://manytools.org/http-html-text/http-request-headers/');
        self::assertStringContainsString("Head2", $res->getBody());
        self::assertStringContainsString("Head1", $res->getBody());
        self::assertStringNotContainsString("Head4", $res->getBody());
    }
}
