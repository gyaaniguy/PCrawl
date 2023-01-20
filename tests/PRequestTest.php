<?php

namespace Gyaaniguy\PCrawl;

use PHPUnit\Framework\TestCase;

class PRequestTest extends TestCase
{

    public function testMain()
    {
        $req = new PRequest();
        $req->setRequestHeaders(
            ['user-agent' => 'Bad bot bat ball boy'] // <- I made a  tongue twister ! 
        )->setUserAgent('Good bot bat ball boy');
        
        $req->opts
            ->setUseTidy(true)
            ->setSleepBetween(100 * 2);

        $page = $req->get('site.com');
        $this->assertStringContainsString('body', $page);
        $this->assertEquals(200, $req->opts->getSleepBetween());
        $this->assertEquals(true, $req->opts->isUseTidy());
        
        $page = $req->get('site.com');
    }
}
