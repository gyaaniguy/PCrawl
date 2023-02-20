<?php

namespace Gyaaniguy\PCrawl\Loggers;

use Gyaaniguy\PCrawl\Response\PResponse;
use Gyaaniguy\PCrawl\Response\PResponseDebug;
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

        $debug->setMustBeHttpCode(99);
        self::assertFalse($debug->isFail());
        $debug->setMustBeHttpCode(100);
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

        $debug->setMustBeHttpCode(200);
        self::assertfalse($debug->isFail());

        $res->setHttpCode(400);
        $debug->setResponse($res);

        $debug->setMustBeHttpCode(200);
        self::assertTrue($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('expected_httpcode', $debug->getFailDetail());
        self::assertArrayHasKey(200, $debug->getFailDetail()['expected_httpcode']);
    }

    public function testBadHttpCode()
    {
        $res = new PResponse();
        $res->setBody("test body");
        $res->setHttpCode(200);

        $debug = new PResponseDebug();
        $debug->setMustNotExistHttpCodes([400, 404]);
        $debug->setResponse($res);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('expected_httpcode', $debug->getFailDetail());

        $res = new PResponse();
        $res->setBody("test body");
        $res->setHttpCode(404);
        $debug = new PResponseDebug();
        $debug->setMustNotExistHttpCodes([400, 404]);
        $debug->setResponse($res);
        self::assertTrue($debug->isFail());
        self::assertArrayHasKey(404, $debug->getFailDetail()['bad_http_code']);
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

    public function testCompareMustExistRegex()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setMustExistRegex(['/test/']);
        $debug->setResponse($res);

        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('good_regex', $debug->getFailDetail());

        $debug->setMustExistRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('good_regex', $debug->getFailDetail());
        self::assertArrayHasKey('/tebadst/', $debug->getFailDetail()['good_regex']);

        $debug->unsetMustExistRegex();
        self::assertFalse($debug->isFail());
        $debug->setMustExistRegex(['/tebadst/']);
        self::assertTrue($debug->isFail());
        $debug->setMustExistRegex(['/test/']);
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

        $debug->setMustNotExistRegex(['/blocked/']);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('bad_regex', $debug->getFailDetail());
        $debug->setMustNotExistRegex(['/test/']);
        self::assertTrue($debug->isFail());

        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('bad_regex', $debug->getFailDetail());
        self::assertArrayHasKey('/test/', $debug->getFailDetail()['bad_regex']);

        $debug->unsetRegexMustNotExist();
        self::assertFalse($debug->isFail());
        $debug->setMustNotExistRegex(['/test/']);
        self::assertTrue($debug->isFail());
        $debug->setMustNotExistRegex(['/block/']);
        self::assertFalse($debug->isFail());
    }

    public function testHasGoodString()
    {
        $debug = new PResponseDebug();
        $res = new PResponse();
        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setMustExistStrings(["bod", "test"]);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('good_string', $debug->getFailDetail());

        $debug->setMustExistStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayHasKey('good_string', $debug->getFailDetail());
        self::assertArrayHasKey('not exists', $debug->getFailDetail()['good_string']);

        $debug->setMustExistStrings(["test"]);
        self::assertFalse($debug->isFail());

        $debug->setMustExistStrings(["not exists"]);
        self::assertTrue($debug->isFail());
        $debug->unsetMustExistStrings();
        self::assertFalse($debug->isFail());

        $debug->setMustExistStrings(["test"]);
        self::assertFalse($debug->isFail());
        $debug->appendToMustExistStrings(["not exists"]);
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

    function testComsethingCustom()
    {
        $res = $this->getRes(400, "Logged In. But you got blocked by cloudflare");

        $loggedInCriteria = new PResponseDebug();
        $loggedInCriteria->setMustExistRegex(['/Logged In/']);
        $loggedInCriteria->setMustNotExistRegex(['/cloudflare/i']);
        $loggedInCriteria->setResponse($res);

        $fourHundredsDetector = new PResponseDebug();
        $fourHundredsDetector->setMustNotExistHttpCodes([400, 404]);

        self::assertTrue($loggedInCriteria->setResponse($res)->isFail());
        self::assertTrue($fourHundredsDetector->setResponse($res)->isFail());

        // NEXT !
        $res = $this->getRes(400, "Logged In. We don't CLOUD FLARE. But you in particular are not welcome");

        $loggedInCriteria = new PResponseDebug();
        $loggedInCriteria->setMustExistRegex(['/Logged In/']);
        $loggedInCriteria->setMustNotExistRegex(['/cloudflare/i']);
        $loggedInCriteria->setResponse($res);

        $fourHundredsDetector = new PResponseDebug();
        $fourHundredsDetector->setMustNotExistHttpCodes([400, 404]);

        self::assertFalse($loggedInCriteria->setResponse($res)->isFail());
        self::assertTrue($fourHundredsDetector->setResponse($res)->isFail());
    }

    public function getRes($httpCode, $body): PResponse
    {
        $res = new PResponse();
        $res->setBody($body);
        $res->setHttpCode($httpCode);
        return $res;
    }
}
  