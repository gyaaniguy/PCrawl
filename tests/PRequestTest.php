<?php

namespace Gyaaniguy\PCrawl;

use PHPUnit\Framework\TestCase;

class PRequestTest extends TestCase
{

    function testMain()
    {
        $req = new PRequest();
        $req->opts
            ->setUseTidy(true)
            ->setSleepBetween(100*2);
        
        $page = $req->get('site.com');
        $this->assertStringContainsString('body',$page);
        $this->assertEquals(200,$req->opts->getSleepBetween());
        $this->assertEquals(true,$req->opts->isUseTidy());
    }
}
