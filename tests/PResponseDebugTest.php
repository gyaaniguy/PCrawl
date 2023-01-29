<?php

namespace Gyaaniguy\PCrawl\Loggers;

use Gyaaniguy\PCrawl\Response\PResponse;
use PHPUnit\Framework\TestCase;

class PResponseDebugTest extends TestCase
{

    public function testHttpCode()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $res->setHttpCode(99);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(99);
        self::assertFalse($debug->isFail());
        $debug->setGoodHttpCode(100);
        self::assertTrue($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('expected_httpcode', $debug->getFailDetail());
        self::assertArrayHasKey(100, $debug->getFailDetail()['expected_httpcode']);

        $debug->unsetGoodHttpCode();
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('expected_httpcode', $debug->getFailDetail());
        $res->setHttpCode(200);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(200);
        self::assertfalse($debug->isFail());

        $res->setHttpCode(400);
        $debug->setResponse($res);

        $debug->setGoodHttpCode(200);
        self::assertTrue($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('expected_httpcode', $debug->getFailDetail());
        self::assertArrayHasKey(200, $debug->getFailDetail()['expected_httpcode']);
    }

    public function testExpectedHeaders()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setResponseHeaders(['content-type: html', 'content-size: 99']);
        $debug->setResponse($res);

        $debug->setContainExpectedHeaders(['content-size: 11']);
        self::assertTrue($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('expected_header', $debug->getFailDetail());
        self::assertArrayHasKey('content-size: 11', $debug->getFailDetail()['expected_header']);

        $debug->setContainExpectedHeaders(['page size']);
        self::assertTrue($debug->isFail());

        $debug->unsetContainExpectedHeaders();
        self::assertFalse($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('expected_header', $debug->getFailDetail());
        $debug->setContainExpectedHeaders(['content-size']);
        self::assertFalse($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('expected_header', $debug->getFailDetail());
    }

    public function testCompareGoodRegex()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setGoodRegex(['/test/']);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('good_regex', $debug->getFailDetail());

        $debug->setGoodRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('good_regex', $debug->getFailDetail());
        self::assertArrayHasKey('/tebadst/', $debug->getFailDetail()['good_regex']);

        $debug->unsetGoodRegex();
        self::assertFalse($debug->isFail());
        $debug->setGoodRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());
        $debug->setGoodRegex(['/test/']);
        self::assertFalse($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('good_regex', $debug->getFailDetail());
    }

    public function testBadRegex()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();

        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setBadRegex(['/blocked/']);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('bad_regex', $debug->getFailDetail());
        $debug->setBadRegex(['/test/']);
        self::assertTrue($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('bad_regex', $debug->getFailDetail());
        self::assertArrayHasKey('/test/', $debug->getFailDetail()['bad_regex']);

        $debug->unsetBadRegex();
        self::assertFalse($debug->isFail());
        $debug->setBadRegex(['/test/']);
        self::assertTrue($debug->isFail());
        $debug->setBadRegex(['/block/']);
        self::assertFalse($debug->isFail());
    }

    public function testHasGoodString()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setGoodStrings(["test"]);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('good_string', $debug->getFailDetail());

        $debug->setGoodStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('good_string', $debug->getFailDetail());
        self::assertArrayHasKey('not exists', $debug->getFailDetail()['good_string']);

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
        $debug = new PResponseDebug();
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
        self::assertIsArray($debug->getFailDetail());
    }
}
  