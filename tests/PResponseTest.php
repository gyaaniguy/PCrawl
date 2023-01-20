<?php

namespace Gyaaniguy\PCrawl;

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
        $res->modBody([[$this, 'toAb']]);
        self::assertEquals("UP THIS", $res->getBody());
    }
}
