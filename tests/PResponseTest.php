<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\Response\PResponse;
use Gyaaniguy\PCrawl\Response\PResponseMods;
use PHPUnit\Framework\TestCase;

class PResponseTest extends TestCase
{
    function toAb($body)
    {
        return strtoupper($body);
    }

    public function testModBody()
    {
        $res = new PResponse();
        $res->setBody("up this");
        $pResponseMods = new PResponseMods($res);
        $pResponseMods->tidy()->toAbsoluteUrls()->addNikhil();
        self::assertContains("nikhil", $res->getBody());

        $pResponseMods->modBody([[$this, 'toAb']]);
        self::assertContains("UP THIS", $res->getBody());
    }
}
