<?php

namespace Gyaaniguy\PCrawl;

use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\HttpClients\CurlCustomClient;
use Gyaaniguy\PCrawl\HttpClients\GuzzleClient;
use Gyaaniguy\PCrawl\Parsing\ParserCommon;
use Gyaaniguy\PCrawl\Request\Request;
use Gyaaniguy\PCrawl\Response\Response;
use Gyaaniguy\PCrawl\Response\ResponseDebug;
use PHPUnit\Framework\TestCase;

class FullExampleTest extends TestCase
{
    public function testFull()
    {
        // simple clients.
        $ch = new CurlClient();
        $gu = new GuzzleClient();

        // Custom Client, that does not allow redirects.
        $uptightNoRedirectClient = new CurlClient();
        $uptightNoRedirectClient->setRedirects(0); // disable redirects

        //Makes debuggers
        $redirectDetector = new ResponseDebug();
        $redirectDetector->setMustNotExistHttpCodes([301, 302, 303, 307, 308]);
        $fullPageDetector = new ResponseDebug();
        $fullPageDetector->setMustExistRegex(['#</html>#']);

        // Make parser
        $parser = new ParserCommon();

        // Start some bad fetching
        $req = new Request();
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


class ConvertToHttpsClient extends CurlCustomClient
{
    public function get(string $url, array $options = []): Response
    {
        $url = str_replace('http://', 'https://', $url);
        return parent::get($url, $options);
    }
}