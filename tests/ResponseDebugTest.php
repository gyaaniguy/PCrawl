<?php

namespace Gyaaniguy\PCrawl\Loggers;

use Gyaaniguy\PCrawl\Response\Response;
use Gyaaniguy\PCrawl\Response\ResponseDebug;
use PHPUnit\Framework\TestCase;

class ResponseDebugTest extends TestCase
{

    public function testHttpCode()
    {
        $debug = new ResponseDebug();
        $res = new Response();
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
        $res = new Response();
        $res->setBody("test body");
        $res->setHttpCode(200);

        $debug = new ResponseDebug();
        $debug->setMustNotExistHttpCodes([400, 404]);
        $debug->setResponse($res);
        self::assertFalse($debug->isFail());
        self::assertIsArray($debug->getFailDetail());
        self::assertArrayNotHasKey('expected_httpcode', $debug->getFailDetail());

        $res = new Response();
        $res->setBody("test body");
        $res->setHttpCode(404);
        $debug = new ResponseDebug();
        $debug->setMustNotExistHttpCodes([400, 404]);
        $debug->setResponse($res);
        self::assertTrue($debug->isFail());
        self::assertArrayHasKey(404, $debug->getFailDetail()['bad_http_code']);
    }

    public function testExpectedHeaders()
    {
        $debug = new ResponseDebug();
        $res = new Response();
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
        $debug = new ResponseDebug();
        $res = new Response();
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
        $debug = new ResponseDebug();
        $res = new Response();

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
        $debug = new ResponseDebug();
        $res = new Response();
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
        $debug = new ResponseDebug();
        $res = new Response();
        $res->setBody("test body");
        $debug->setResponse($res);

        $debug->setCustomFailCondition(function (Response $res) {
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

    public function testComsethingCustom()
    {
        $res = $this->getRes(400, "Logged In. But you got blocked by cloudflare");

        $loggedInCriteria = new ResponseDebug();
        $loggedInCriteria->setMustExistRegex(['/Logged In/']);
        $loggedInCriteria->setMustNotExistRegex(['/cloudflare/i']);
        $loggedInCriteria->setResponse($res);

        $fourHundredsDetector = new ResponseDebug();
        $fourHundredsDetector->setMustNotExistHttpCodes([400, 404]);

        self::assertTrue($loggedInCriteria->setResponse($res)->isFail());
        self::assertTrue($fourHundredsDetector->setResponse($res)->isFail());

        // NEXT !
        $res = $this->getRes(400, "Logged In. We don't CLOUD FLARE. But you in particular are not welcome");

        $loggedInCriteria = new ResponseDebug();
        $loggedInCriteria->setMustExistRegex(['/Logged In/']);
        $loggedInCriteria->setMustNotExistRegex(['/cloudflare/i']);
        $loggedInCriteria->setResponse($res);

        $fourHundredsDetector = new ResponseDebug();
        $fourHundredsDetector->setMustNotExistHttpCodes([400, 404]);

        self::assertFalse($loggedInCriteria->setResponse($res)->isFail());
        self::assertTrue($fourHundredsDetector->setResponse($res)->isFail());
    }

    public function getRes($httpCode, $body): Response
    {
        $res = new Response();
        $res->setBody($body);
        $res->setHttpCode($httpCode);
        return $res;
    }
}
  