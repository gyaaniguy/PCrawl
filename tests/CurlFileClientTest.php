<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\PRequest;
use PHPUnit\Framework\TestCase;

class CurlFileClientTest extends TestCase
{
    public function testGetFile()
    {
        $req = new PRequest();

        $fileClient = new CurlClient();        
        $req->setClient($fileClient);
        

        $req->getFile('https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', ['filepath' =>'/tmp/google.png']);
        self::assertFileExists('/tmp/google.png');
    }

    public function testDefaultOptions()
    {
        $req = new PRequest();
        $fileClient = new CurlFileClient();
        $curlClient = new CurlClient();

        $req->setClient($curlClient)->setUserAgent('curl bot');
        // req get test file fetch
        $options = $req->getClientOptions();
        self::assertEquals('curl bot', $options['user_agent']);

        $req->setClient($fileClient);
        $options = $req->getClientOptions();
        self::assertEquals('File bot', $options['user_agent']);
    }
}
