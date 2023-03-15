<?php

namespace Gyaaniguy\PCrawl\HttpClients;

use Gyaaniguy\PCrawl\Request\Request;
use PHPUnit\Framework\TestCase;

class GuzzleFileClientTest extends TestCase
{
    public function testGetFile()
    {
        $req = new Request();

        $fileClient = new GuzzleClient();
        $req->setClient($fileClient);

        $filePath = '/tmp/google.png';
        if (is_file($filePath) && !is_dir($filePath)) {
            unlink($filePath);
        }
        $res = $req->getFile(
            'https://www.google.com/images/branding/googlelogo/2x/googlelogo_color_272x92dp.png',
            ['file_path' => $filePath]
        );
        self::assertFileExists($filePath);
    }
}
