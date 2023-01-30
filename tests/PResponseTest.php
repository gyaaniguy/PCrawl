<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\Response\PResponse;
use Gyaaniguy\PCrawl\Response\PResponseMods;
use PHPUnit\Framework\TestCase;

class PResponseTest extends TestCase
{
    public function uppercase($body): string
    {
        return strtoupper($body);
    }

    public function testModBody()
    {
        $res = new PResponse();
        $res->setBody("up this");
        $pResponseMods = new PResponseMods($res);
        $pResponseMods->tidy()->toAbsoluteUrls()->addNikhil();
        self::assertStringContainsString("nikhil", $res->getBody());

        $pResponseMods->modBody([[$this, 'uppercase']]);
        self::assertStringContainsString("UP THIS", $res->getBody());
    }
}
