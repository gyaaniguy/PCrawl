<?php

namespace Gyaaniguy\PCrawl\Factories;

use Gyaaniguy\PCrawl\HttpClients\AbstractHttpClient;
use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\HttpClients\GuzzleClient;
use PHPUnit\Framework\TestCase;

class RequestFactoryTest extends TestCase
{
    public function testCreate()
    {
        $req = RequestFactory::create();
        $req->getClient()->setUserAgent("Gyaaniguy");
        $this->assertInstanceOf(AbstractHttpClient::class, $req->getClient());

        $req = RequestFactory::create(new CurlClient());
        $this->assertInstanceOf(CurlClient::class, $req->getClient());

        $req = RequestFactory::create(new GuzzleClient());
        $this->assertInstanceOf(GuzzleClient::class, $req->getClient());
        $req->setClient(new CurlClient());
        $this->assertInstanceOf(CurlClient::class, $req->getClient());
    }

    public function testFetchWithFactory()
    {
        $res = RequestFactory::create()->get('http://www.whatsmyua.info/');
        $this->assertStringContainsStringIgnoringCase('my user agent', $res->getBody());
    }
    
    public function testGetClient()
    {
        // supply client to factory:
        $req = RequestFactory::create();
        $client = $req->getClient();
        $client->setUserAgent('another bot');
        $res = $req->get('http://www.whatsmyua.info');
        $this->assertStringContainsStringIgnoringCase('another bot', $res->getBody());

    }
}
