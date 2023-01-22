<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\Response\PResponse;
use PHPUnit\Framework\TestCase;

class PRequestTest extends TestCase
{

    public function testMain()
    {
        $req = new PRequest();
        $req
            ->setRequestHeaders(['user-agent' => 'Bad bot bat ball boy']) // <- tongue twister ! 
            ->setUserAgent('Good bot bat ball boy');

//        $page = $req->get('site.com');
//        $this->assertStringContainsString('body', $page);
//        $this->assertEquals(200, $req->opts->getSleepBetween());
//        $this->assertEquals(true, $req->opts->isUseTidy());
//        $page = $req->get('site.com');
    }

    public function testCustomClient()
    {
        $req = new PRequest();
        $req->setUserAgent("default user");
        $options = $req->getOptions();
        self::assertArrayHasKey("user_agent", $options);
        self::assertEquals("default user",$options["user_agent"]);
        
        // Bro bot user agent.
        $options = $req->setClient(new Brobot())->getOptions();      
        $this->assertEquals("Bro bot", $options['user_agent']);
        $res = $req->get('http://34.205.169.194/test/');
//        self::assertContains("Bro bot",$res->getBody());
//        $req->setUserAgent("no bro");
//        $res = $req->get('http://34.205.169.194/test/');
//        self::assertContains("no bro",$res->getBody());
        
        // Only return Head Client
        $options = $req->setClient(new OnlyHeadClient())->setUserAgent("only head")->getOptions();
        $this->assertEquals("only head", $options['user_agent']);
        $onlyHeadRes = $req->get('icanhazip.com');
        self::assertContains("HTTP",$onlyHeadRes->getBody());
        self::assertNotContains("html",$onlyHeadRes->getBody());



    }
}

class Brobot extends CurlClient
{
    public array $defaultOptions = [
        'user_agent'            => 'Bro bot',
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
        ]
    ];
    public function customClientOptions(array $customClientOptions) // <- Not needed. Exists in parent
    {
        curl_setopt_array($this->ch, $customClientOptions);
    }
}