<?php

use Gyaaniguy\PCrawl\HttpClients\CurlClient;
use Gyaaniguy\PCrawl\HttpClients\CurlCustomClient;
use Gyaaniguy\PCrawl\HttpClients\GuzzleClient;
use Gyaaniguy\PCrawl\Request\PRequest;
use Gyaaniguy\PCrawl\Response\PResponse;
use Gyaaniguy\PCrawl\Response\PResponseDebug;
use Gyaaniguy\PCrawl\Response\PResponseMods;

// Search  government database website for different keywords and make a list of all the datasets available. do some pagination

// Lets make some  clients
$ch = new CurlClient();
$gu = new GuzzleClient();
$uptightNoRedirectClient = new CurlClient();
$uptightNoRedirectClient->setRedirects(0); // disable redirects

class ConvertToHttpsClient extends CurlCustomClient
{
    public function get(string $url, array $options = []): PResponse
    {
        $url = str_replace('http://', 'https://', $url);
        return parent::get($url, $options);
    }
}

// Set up debugging. Observe how this can be done once and reused.
$redirectDetector = new PResponseDebug();
$redirectDetector->setMustNotExistHttpCodes([301, 302, 303, 307, 308]);

$fullPageDetector = new PResponseDebug();
$fullPageDetector->setMustExistRegex(['#</html>#']);

// Set up requests
$req = new PRequest();

// Start some bad fetching
//-------------------------------------------------------------
// Fetch page > set debugger to detect 301. modifying client to follow 301. Fetch page again.
$url = "url_that_returns_301.com";
$res = $req->setClient($uptightNoRedirectClient)->get($url);
$redirectDetector->setResponse($res);

$count = 0;
do {
    $uptightNoRedirectClient->setRedirects(2);
    $res = $req->get($url);
    if ($redirectDetector->isFail()) {
        var_dump($redirectDetector->getFailDetail());
        $uptightNoRedirectClient->setRedirects(1);
        $res = $req->get($url);
    }
} while ($redirectDetector->isFail() && $count++ < 1);

//-------------------------------------------------------------
// Fetch page > set debugger to detect broken page. Break page, by using PRresponseMods. Fix page with PRresponseMods.
$url = "https://google.com";
$res = $req->setClient($gu)->get($url);
$redirectDetector->setResponse($res);
$pResponseMods = new PResponseMods($res);
// deliberately break html with mod. In real world you will probably not do this.
$pResponseMods->modBody([
    function ($body) {
        return preg_replace('#</html>#', '', $body);
    }
]);
$fullPageDetector->setResponse($res);
if ($fullPageDetector->isFail()) {
    var_dump($redirectDetector->getFailDetail());
    // use mod to fix html
    $pResponseMods->tidy();
}