<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\PRequest;
use PHPUnit\Framework\TestCase;

class CurlFileClientTest extends TestCase
{
    public function testGetFile()
    {
        $req = new PRequest();
        $fileClient = new CurlFileClient();        
        
        $curlClient = new CurlClient();    
        $req->setClient($fileClient)->setUserAgent('file_bot');
        $fileBotOpts = $req->getOptions();
        $req->setClient($curlClient)->setUserAgent('hi');        
        // req get test file fetch

        $req->setOptions($fileBotOpts)->get('https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', ['filepath' =>'/tmp/google.png']);
        $options = $req->getOptions();
        self::assertFileExists('/tmp/google.png');
        self::assertEquals('file_bot', $options['user_agent']);
    }
}
