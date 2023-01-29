<?php

namespace Gyaaniguy\PCrawl\Loggers;

use Gyaaniguy\PCrawl\Response\PResponse;
use PHPUnit\Framework\TestCase;

class ResponseDebugTest extends TestCase
{

    public function testHttpCode()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $res->setHttpCode(99);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(99);
        self::assertFalse($debug->isFail());
        $debug->setGoodHttpCode(100);
        self::assertTrue($debug->isFail());
        $debug->unsetGoodHttpCode();
        self::assertFalse($debug->isFail());

        $res->setHttpCode(200);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(200);
        self::assertfalse($debug->isFail());

        $res->setHttpCode(400);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(200);
        self::assertTrue($debug->isFail());

    }
    public function testExpectedHeaders()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();
        $res->setResponseHeaders(['content-type: html','content-size: 99']);

        $debug->setResponse($res);


        $debug->setContainExpectedHeaders(['content-size: 11']);
        self::assertTrue($debug->isFail());
        $debug->setContainExpectedHeaders(['page size']);
        self::assertTrue($debug->isFail());
        $debug->unsetContainExpectedHeaders();
        self::assertFalse($debug->isFail());
        $debug->setContainExpectedHeaders(['content-size']);
        self::assertFalse($debug->isFail());

    }

    public function testCompareGoodRegex()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setGoodRegex(['/test/']);
        self::assertFalse($debug->isFail());
        $debug->setGoodRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());
        $debug->unsetGoodRegex();
        self::assertFalse($debug->isFail());
        $debug->setGoodRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());
        $debug->setGoodRegex(['/test/']);
        self::assertFalse($debug->isFail());
    }
    public function testBadRegex()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();

        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setBadRegex(['/blocked/']);
        self::assertFalse($debug->isFail());
        $debug->setBadRegex(['/test/']);
        self::assertTrue($debug->isFail());
        $debug->unsetBadRegex();
        self::assertFalse($debug->isFail());
        $debug->setBadRegex(['/test/']);
        self::assertTrue($debug->isFail());
        $debug->setBadRegex(['/block/']);
        self::assertFalse($debug->isFail());
        
    }
    public function testHasGoodString()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);
        
        $debug->setGoodStrings(["test"]);        
        self::assertFalse($debug->isFail());
        $debug->setGoodStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        $debug->setGoodStrings(["test"]);
        self::assertFalse($debug->isFail());

        $debug->setGoodStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        $debug->unsetGoodStrings();
        self::assertFalse($debug->isFail());


        $debug->setGoodStrings(["test"]);
        self::assertFalse($debug->isFail());
        $debug->appendGoodStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        
    }
    
    
    public function testCallbackFail()
    {
        $debug = new ResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);
        
        $debug->setCustomFailCondition(function (PResponse $res) {
            if (strlen($res->getBody()) < 10) {
                return false;
            }
            return true;
        });
        self::assertTrue($debug->isFail());
        $res->setBody("test big body");
        $debug->setResponse($res);
        self::assertFalse($debug->isFail());
        
    }
}
  