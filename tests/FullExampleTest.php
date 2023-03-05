<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\HttpClients\PCurlClient;
use Gyaaniguy\PCrawl\HttpClients\PCurlCustomClient;
use Gyaaniguy\PCrawl\HttpClients\PGuzzleClient;
use Gyaaniguy\PCrawl\Parsing\PParserCommon;
use Gyaaniguy\PCrawl\Request\PRequest;
use Gyaaniguy\PCrawl\Response\PResponse;
use Gyaaniguy\PCrawl\Response\PResponseDebug;
use PHPUnit\Framework\TestCase;

class FullExampleTest extends TestCase
{
    public function testFull()
    {
        // simple clients.
        $ch = new PCurlClient();
        $gu = new PGuzzleClient();

        // Custom Client, that does not allow redirects.
        $uptightNoRedirectClient = new PCurlClient();
        $uptightNoRedirectClient->setRedirects(0); // disable redirects

        //Makes debuggers
        $redirectDetector = new PResponseDebug();
        $redirectDetector->setMustNotExistHttpCodes([301, 302, 303, 307, 308]);
        $fullPageDetector = new PResponseDebug();
        $fullPageDetector->setMustExistRegex(['#</html>#']);

        // Make parser
        $parser = new PParserCommon();

        // Start some bad fetching
        $req = new PRequest();
        $url = "http://www.whatsmyua.info";
        $req->setClient($uptightNoRedirectClient);
        $count = 0;
        do {
            $res = $req->get($url);
            $redirectDetector->setResponse($res);
            if ($redirectDetector->isFail()) {
//                var_dump($redirectDetector->getFailDetail());
                $uptightNoRedirectClient->setRedirects(1);
                $res = $req->get($url);
            }
        } while ($redirectDetector->isFail() && $count++ < 1);
        if ($fullPageDetector->setResponse($res)->isFail()) {
//            var_dump($redirectDetector->getFailDetail());
        } else {
            $h1 = $parser->setResponse($res->getBody())->find('h1')->text();
            $htmlClass = $parser->find('html')->attr('class');
            self::assertStringContainsStringIgnoringCase('user agent', $h1);
            self::assertStringContainsStringIgnoringCase('no-js', $htmlClass);
        }
    }
}


class ConvertToHttpsClient extends PCurlCustomClient
{
    public function get(string $url, array $options = []): PResponse
    {
        $url = str_replace('http://', 'https://', $url);
        return parent::get($url, $options);
    }
}