<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class CurlFileClientTest extends TestCase
{
    public function testGetFile()
    {
        $req = new Request();

        $fileClient = new CurlClient();
        $req->setClient($fileClient);

        $req->getFile(
            'https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
            ['file_path' => '/tmp/google.png']
        );
        self::assertFileExists('/tmp/google.png');
    }
}
