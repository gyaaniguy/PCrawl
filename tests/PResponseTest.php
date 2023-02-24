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
    public function removeSpaces($body): string
    {
        return preg_replace('/\s+/','',$body);
    }

    public function testModBody()
    {
        $res = new PResponse();
        $res->setBody("up this");
        $pResponseMods = new PResponseMods($res);
        $pResponseMods->tidy();
        self::assertStringContainsString("up this", $res->getBody());

        $pResponseMods->modBody([[$this, 'uppercase'], [$this, 'removeSpaces']]);
        self::assertStringContainsString("UPTHIS", $res->getBody());
    }
}
