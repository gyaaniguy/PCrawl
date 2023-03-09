<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class GuzzleClientTest extends TestCase
{
    public function testSetRedirects()
    {
        $req = new Request();
        $client = new GuzzleClient();
        $client->setRedirects(4);
        $req->setClient($client);
        $clientOptions = $client->getOptions();
        self::assertArrayHasKey('redirect_num', $clientOptions);
        self::assertEquals(4, $clientOptions['redirect_num']);

        $client->setRedirects(0);
        $res = $req->get('http://whatsmyua.info/');
        self::assertEquals(301, $res->getHttpCode());
    }

    public function testSetUserAgent()
    {
        $req = new Request();
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
        $req = new Request($client);
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
        $req = new Request($client);
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

    public function testCookies()
    {
        $g = new GuzzleClient();
        $g->cookies(true);
        $req = new Request($g);
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringNOTContainsString("PHPSESSID", $res->getBody());
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringContainsString("PHPSESSID", $res->getBody());
        $g->cookies(false);
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringNotContainsString("PHPSESSID", $res->getBody());
    }

    public function testClearCookies()
    {
        $g = new GuzzleClient();
        $this->_testClearCookiesForClient($g);
        $c = new CurlClient();
        $this->_testClearCookiesForClient($c);
    }

    public function _testClearCookiesForClient($g)
    {
        $g->cookies(true);
        $req = new Request($g);
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringNOTContainsString("PHPSESSID", $res->getBody());
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringContainsString("PHPSESSID", $res->getBody());

        $g->clearCookies();
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringNotContainsString("PHPSESSID", $res->getBody());
        $res = $req->get('https://www.myhttpheader.com/');
        self::assertStringContainsString("PHPSESSID", $res->getBody());
    }


}
