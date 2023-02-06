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
        $req->setClient($fileClient)->get('https://www.google.com/file.png', ['filepath' =>'/tmp/google.png']);

        $curlClient = new CurlClient();
        $req->setClient($curlClient)->get('https://www.google.com');
        
        
        self::assertFileExists('/tmp/google.png');
        unlink('/tmp/google.png');
        
        $fileBotOpts = $req->getOptions();
        $req->setClient($curlClient)->setUserAgent('hi');        
        // req get test file fetch

        $req->setOptions($fileBotOpts)->get('https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png', ['filepath' =>'/tmp/google.png']);
        $options = $req->getOptions();
        self::assertFileExists('/tmp/google.png');
        self::assertEquals('file_bot', $options['user_agent']);
    }

    public function testDefaultOptions()
    {
        $req = new PRequest();
        $fileClient = new CurlFileClient();
        $curlClient = new CurlClient();

        $req->setClient($curlClient)->setUserAgent('curl bot');
        // req get test file fetch
        $options = $req->getOptions();
        self::assertEquals('curl bot', $options['user_agent']);

        $req->setClient($fileClient);
        $options = $req->getOptions();
        self::assertEquals('File bot', $options['user_agent']);
    }
}
