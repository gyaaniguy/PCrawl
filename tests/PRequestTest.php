<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\HttpClients\CurlCustomClient;
use Gyaaniguy\PCrawl\Response\PResponse;
use PHPUnit\Framework\TestCase;

class PRequestTest extends TestCase
{

    public function testSetClient()
    {
        $broBot = new Brobot();

        $req = new PRequest();
        $req->setClient($broBot);
        $res = $req->get('https://manytools.org/http-html-text/http-request-headers/');
        self::assertStringContainsStringIgnoringCase("Bro bot", $res->getBody());
        self::assertStringContainsString("Head2", $res->getBody());
        self::assertStringContainsString("val 2", $res->getBody());
        self::assertStringNotContainsString("val 3", $res->getBody());


        // Bro bot user agent.
        $boringCurl = new CurlClient();
        $boringCurl->setUserAgent("boring curl");
        $options = $req->setClient($boringCurl);
        $res = $req->get('https://manytools.org/http-html-text/http-request-headers/');
        self::assertStringContainsString("oring curl", $res->getBody());
        self::assertStringNotContainsString("Head2", $res->getBody());
        $boringCurl->setUserAgent("wanna be bro");
        $res = $req->get('https://www.whatsmyua.info/');
        self::assertStringContainsString("wanna be bro", $res->getBody());
    }



//    public function testBranch()
//    {
//        $req = new PRequest();
//        $broBot = new Brobot();
//        $client = new GuzzleClient();
//        
//        // test brobot client default options
//        $req->setClient($broBot);
//        $reqOptions = $req->getClientOptions();
//        self::assertEquals('Bro bot', $reqOptions['user_agent']);
//        self::assertEquals($broBot, $reqOptions['http_client']);
//
//        //deep copy test
//        $newReq = $req->branch();
//        $newReq->setUserAgent("branched");
//        self::assertInstanceOf('\Gyaaniguy\PCrawl\PRequest', $newReq);
//        
//        $newReqOptions = $newReq->getClientOptions();
//        self::assertEquals('branched', $newReqOptions['user_agent']);        
//        self::assertEquals($broBot, $newReqOptions['http_client']);
//        
//        $newReq->setClient($curlClient);
//        $newReqOptions = $newReq->getClientOptions();
//        self::assertEquals($curlClient, $newReqOptions['http_client']);
//
//        $reqOptions = $req->getClientOptions();
//        self::assertEquals($broBot, $reqOptions['http_client']);
//
//        //Shallow copy test
//        $req = new PRequest();
//        $curlClientShallow = new CurlClient();
//        $req->setClient($curlClientShallow);
//        $reqOptions = $req->getClientOptions();
//        self::assertEquals($curlClientShallow, $reqOptions['http_client']);
//
//        $newReq = $req;
//        $broBotShallowTest = new Brobot();
//        $newReq->setClient($broBotShallowTest);
//        $newReqOptions = $newReq->getClientOptions();
//        self::assertEquals($broBotShallowTest, $newReqOptions['http_client']);
//
//        $reqOptions = $req->getClientOptions();
//        self::assertEquals($broBotShallowTest, $reqOptions['http_client']);
//
//
//    }

    public function testWebscriptClient()
    {
        $webScriptClient = new WebScriptClient();
        $webScriptClient->strictHttps(false)->setRedirects(3);
        $req = new PRequest($webScriptClient);

        $res = $req->get('https://www.icanhazip.com', ['unblock_site' => 'getIp']);
        self::assertStringContainsStringIgnoringCase("User Agent", $res->getBody());
        self::assertStringContainsStringIgnoringCase("we use you", $res->getBody());
    }

}

class Brobot extends CurlCustomClient
{
    public array $clientOptions = [
        CURLOPT_USERAGENT => 'Bro bot',
        CURLOPT_HTTPHEADER => [
            'head1: val 1',
            'head2: val 2',
        ],
    ];
}

class WebScriptClient extends CurlClient
{
    public array $clientOptions = [
        'user_agent' => 'We use you',
    ];

    public function get(string $url, array $options = []): PResponse
    {
        if (!empty($options['unblock_site'])) {
            switch ($options['unblock_site']) {
                case 'anonymouse':
                    $url = 'https://anonymouse.com?url=' . urlencode($url);
                    break;
                case 'unblock.com':
                    $url = 'https://unblock.com?url=' . urlencode($url);
                    return parent::post($url, ['token' => 'letmein']);
                    break;
                case 'getIp':
                    $url = 'https://www.whatsmyua.info';
                    return parent::get($url);
                    break;
            }
        }
        return parent::get($url, $options);
    }
}