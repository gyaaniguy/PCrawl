<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\Response\Response;
use Gyaaniguy\PCrawl\Response\ResponseMods;
use PHPUnit\Framework\TestCase;

class ResponseTest extends TestCase
{
    public function uppercase($body): string
    {
        return strtoupper($body);
    }

    public function removeSpaces($body): string
    {
        return preg_replace('/\s+/', '', $body);
    }

    public function testModBody()
    {
        $res = new Response();
        $res->setBody("up this");
        $pResponseMods = new ResponseMods($res);
        $pResponseMods->tidy();
        self::assertStringContainsString("up this", $res->getBody());

        $pResponseMods->modBody([[$this, 'uppercase'], [$this, 'removeSpaces']]);
        self::assertStringContainsString("UPTHIS", $res->getBody());
    }
}
