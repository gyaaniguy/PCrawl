<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\Response\PResponse;
use PHPUnit\Framework\TestCase;

class PRequestTest extends TestCase
{

    public function testSetCustomClientOptions()
    {
        
        $req = new PRequest();
        $req->closeConnection();
        $req->enableCookies();
        $req = new PRequest();
        $req->setCustomClientOptions([
            CURLOPT_HEADER => 1,
            CURLOPT_NOBODY => 1,
        ]);
        $onlyHeadRes = $req->get('icanhazip.com');
        
        self::assertStringContainsString("HTTP", $onlyHeadRes->getBody());
        self::assertStringNotContainsString("html", $onlyHeadRes->getBody());
    }

    // Todo Set client as guzzle, then back to curl. Tests to make sure all options are still ok
    public function testUseCurl()
    {
        $req = new PRequest();
        $client = $req->getHttpClient();
        self::assertIsObject($client);
        self::assertObjectHasAttribute('res', $client);
    }

    public function testSetClient()
    {
        $req = new PRequest();
        $req->setUserAgent("default user");
        $options = $req->getOptions();
        self::assertArrayHasKey("user_agent", $options);
        self::assertEquals("default user", $options["user_agent"]);

        // Bro bot user agent.
        $options = $req->setClient(new Brobot())->getOptions();
        $this->assertEquals("Bro bot", $options['user_agent']);
        $this->assertEquals([
            'head1 : val 1',
            'head2 : val 2',
        ], $options['headers']);
        $this->assertEquals(3, $options["redirect_num"]);
        $this->assertEquals(true, $options["https"]);
        $res = $req->allowHttps()->get('http://www.xhaus.com/headers');
        self::assertStringContainsString("Bro bot", $res->getBody());
        self::assertStringContainsString("Head2", $res->getBody());
        self::assertStringContainsString("val 2", $res->getBody());
        self::assertStringNotContainsString("val 3", $res->getBody());
        $req->setUserAgent("no bro");
        $res = $req->get('https://www.whatsmyua.info/');
        self::assertStringContainsString("no bro", $res->getBody());

        // Only return Head Client
        $options = $req->setClient(new OnlyHeadClient())->setUserAgent("only head")->getOptions();
        $this->assertEquals("only head", $options['user_agent']);
        $onlyHeadRes = $req->get('icanhazip.com');
        self::assertStringContainsString("HTTP", $onlyHeadRes->getBody());
        self::assertStringNotContainsString("html", $onlyHeadRes->getBody());
    }

    public function testSetRedirects()
    {
        $req = new PRequest();
        $req->setRedirects(4);
        $options = $req->getOptions();
        self::assertArrayHasKey('redirect_num', $options);
        self::assertEquals(4, $options['redirect_num']);
    }

    public function testAllowHttps()
    {
        $req = new PRequest();
        $req->allowHttps();
        $options = $req->getOptions();
        self::assertArrayHasKey('https', $options);
        self::assertTrue($options['https']);
    }

    public function testHeaders()
    {
        $req = new PRequest();
        $headers = [
            "accept: *",
            "content-type: application/json",
        ];
        $req->setRequestHeaders($headers);
        $options = $req->getOptions();
        self::assertArrayHasKey('headers', $options);
        self::assertIsArray($options['headers']);
        self::assertEquals($headers, $options['headers']);

        $additionalHeaders = ["x-fetch: Made Up Thing", "accept: nil"];
        $req->addRequestHeaders($additionalHeaders);
        $options = $req->getOptions();
        self::assertArrayHasKey('headers', $options);
        self::assertIsArray($options['headers']);
        self::assertEquals([
            "accept: nil",
            "content-type: application/json",
            "x-fetch: Made Up Thing",
        ], $options['headers']);
    }

    public function testSetUserAgent()
    {
        $req = new PRequest();
        $userAgentStr = "user agent test";
        $req->setUserAgent($userAgentStr);
        $options = $req->getOptions();
        self::assertArrayHasKey('user_agent', $options);
        self::assertEquals($userAgentStr, $options['user_agent']);
        $res = $req->get('https://www.whatsmyua.info/');
        self::assertStringContainsString("user agent test", $res->getBody());
    }

    public function testCookies()
    {
        $req = new PRequest();
        $req->enableCookies();
        self::assertObjectHasAttribute('cookiePath', $req);
        self::assertIsString($req->getCookiePath());
        $req->closeConnection();

        $req->disableCookies();
        $options = $req->getOptions();
        self::assertArrayHasKey('enable_cookies', $options);
        self::assertFalse($options['enable_cookies']);

        $req->enableCookies();
        self::assertObjectHasAttribute('cookiePath', $req);
        self::assertIsString($req->getCookiePath());

        touch($req->getCookiePath());
        self::assertFileExists($req->getCookiePath());
        $req->clearCookies();
        self::assertIsString($req->getCookiePath());
        self::assertFileNotExists($req->getCookiePath());

        $req->allowHttps()->get('https://google.com/');
        $req->closeConnection();
        self::assertFileExists($req->getCookiePath()); // curl must close for this to exist.
    }

    public function testOptions()
    {
        $req = new PRequest();
        $req->setCustomClientOptions([
            CURLOPT_NOBODY => 0,
        ]);
        $req->setRedirects(4);
        $headers = [
            "accept: *",
            "content-type: application/json",
        ];
        $req->setRequestHeaders($headers);
        $userAgentStr = "custom agent";
        $req->setUserAgent($userAgentStr);
        $req->allowHttps();
        $req->enableCookies();

        $optionsOriginal = $req->getOptions();
        $newReq = new PRequest($optionsOriginal);
        $newReqOptions = $newReq->getOptions();
        self::assertEquals(4, $newReqOptions['redirect_num']);
        self::assertEquals('custom agent', $newReqOptions['user_agent']);
        self::assertEquals(true, $newReqOptions['https']);
        self::assertEquals(true, $newReqOptions['enable_cookies']);
        self::assertEquals($headers, $newReqOptions['headers']);
    }
    
    public function testBranch()
    {
        $req = new PRequest();
        $broBot = new Brobot();
        $curlClient = new CurlClient();
        $req->setClient($broBot);
        $reqOptions = $req->getOptions();
        self::assertEquals('Bro bot', $reqOptions['user_agent']);
        self::assertEquals($broBot, $reqOptions['http_client']);

        //deep copy test
        $newReq = $req->branch();
        $newReq->setUserAgent("branched");
        self::assertInstanceOf('\Gyaaniguy\PCrawl\PRequest', $newReq);
        
        $newReqOptions = $newReq->getOptions();
        self::assertEquals('branched', $newReqOptions['user_agent']);        
        self::assertEquals($broBot, $newReqOptions['http_client']);
        
        $newReq->setClient($curlClient);
        $newReqOptions = $newReq->getOptions();
        self::assertEquals($curlClient, $newReqOptions['http_client']);

        $reqOptions = $req->getOptions();
        self::assertEquals($broBot, $reqOptions['http_client']);

        //Shallow copy test
        $req = new PRequest();
        $curlClientShallow = new CurlClient();
        $req->setClient($curlClientShallow);
        $reqOptions = $req->getOptions();
        self::assertEquals($curlClientShallow, $reqOptions['http_client']);

        $newReq = $req;
        $broBotShallowTest = new Brobot();
        $newReq->setClient($broBotShallowTest);
        $newReqOptions = $newReq->getOptions();
        self::assertEquals($broBotShallowTest, $newReqOptions['http_client']);

        $reqOptions = $req->getOptions();
        self::assertEquals($broBotShallowTest, $reqOptions['http_client']);


    }
}

class Brobot extends CurlClient
{
    public array $defaultOptions = [
        'user_agent'   => 'Bro bot',
        'headers'      => [
            'head1 : val 1',
            'head2 : val 2',
        ],
        'redirect_num' => 3,
        'https'        => true,
    ];
}

class WebScriptClient extends CurlClient
{
    public array $defaultOptions = [
        'user_agent'            => 'We use you',
    ];
    public function get(string $url, array $options = []): PResponse
    {
        if (empty($options['unblock_site']) && filter_var($options['unblock_site'], FILTER_VALIDATE_URL)) {
            switch ($options['site']) {
                case 'anonymouse':
                    $url = 'https://anonymouse.com?url='.urlencode($url);
                    break; 
                case 'unblock.com':
                    $url = 'https://unblock.com?url='.urlencode($url);
                    return parent::post($url,['token' => 'letmein']);
                    break;
            }
        } 
        return parent::get($url,$options);
    }
}


class OnlyHeadClient extends CurlClient
{
    public array $defaultOptions = [
        'custom_client_options' => [
            CURLOPT_HEADER => 1,
            CURLOPT_NOBODY => 0,
        ],
        'user_agent'            => 'Bro bot',
    ];
    public function setCustomClientOptions(array $customClientOptions): void // <- Not needed. Exists in parent
    {
        curl_setopt_array($this->ch, $customClientOptions);
    }
}