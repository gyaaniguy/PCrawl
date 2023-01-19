<?php

namespace Gyaaniguy\PCrawl;

use PHPUnit\Framework\TestCase;



class PResponseTest extends TestCase
{
    function toAb($body){
        return strtoupper($body);
    }
    public function testModBody()
    {
        $res = new PResponse();
        $res->body = "no";
        $res->modBody([[$this,'toAb']]);
        self::assertEquals("NO",$res->body);

    }
}
