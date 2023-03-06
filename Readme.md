## This is in alpha stage.

# PCrawl

PCrawl is a PHP library for crawling and scraping web pages.   
It supports multiple clients: curl, guzzle. Options to debug, modify and parse responses.

## Features

- Rapidly create custom clients. Fluently change clients and client options like user-agent, with method chaining.
- Responses can be modified using reusable callback functions. 
- Debug Responses using different criterias - httpcode, regex etc.
- Parse responses using querypath library. Several convenience functions are provided.
- Fluent API. Different debuggers, clients and response mod objects can be be changed on the fly !

## Full Example

We'll try to fetch a bad page, then detect using a debugger and finally change client options to fetch the page correctly.

- Setup up some clients

```php
// simple clients.
$gu = new PGuzzleClient();

// Custom Client, that does not allow redirects.
$uptightNoRedirectClient = new PCurlClient();
$uptightNoRedirectClient->setRedirects(0); // disable redirects

// Custom client - thin wrapper around curl
class ConvertToHttpsClient extends PCurlCustomClient
{
    public function get(string $url, array $options = []): PResponse
    {
        $url = str_replace('http://', 'https://', $url);
        return parent::get($url, $options);
    }
}
```

- Lets make some debugger objects
```php
$redirectDetector = new PResponseDebug();
$redirectDetector->setMustNotExistHttpCodes([301, 302, 303, 307, 308]);
$fullPageDetector = new PResponseDebug();
$fullPageDetector->setMustExistRegex(['#</html>#']);
```

##### Start fetching!

For testing, we will fetch page with a client that does not support redirects, then use the redirectDetector to detect
301. If so we change client option to support redirects and fetch again.

```php
$req = new PRequest();
$url = "http://www.whatsmyua.info";
$req->setClient($uptightNoRedirectClient);
$count = 0;
do {
    $res = $req->get($url);
    $redirectDetector->setResponse($res);
    if ($redirectDetector->isFail()) {
        var_dump($redirectDetector->getFailDetail());
        $uptightNoRedirectClient->setRedirects(1);
        $res = $req->get($url);
    }
} while ($redirectDetector->isFail() && $count++ < 1);
```
Use the fullPageDetector to detect if the page is proper.   
Then parse the response body using PParser
```php
if ($fullPageDetector->setResponse($res)->isFail()) {
    var_dump($redirectDetector->getFailDetail());
} else {
    $h1 = $parser->setResponse($res->getBody())->find('h1')->text();
    $htmlClass = $parser->find('html')->attr('class');
}
```

> Note: the debuggers, clients, parsers can be reused.

### Detailed Usage

Usage of functions can be divided into parts:

* [Fetching a page](docs/Fetching.md)
* [Modifying the response body](docs/Modify_Response.md)
* [Debugging the response](docs/Debugging_Response.md)
* [Parsing the response body](docs/Parse_Response.md)

## Installation

- Composer:
```bash
composer init   # for new projects. 
composer config minimum-stability dev # Will be removed once stable.
composer require gyaaniguy/pcrawl
composer update
include __DIR__ . '/vendor/autoload.php'; #in PHP
```
-  github:  

```bash
git clone git@github.com:gyaaniguy/PCrawl.git # clone repo 
cd PCrawl 
composer update # update composer 
mv ../PCrawl /desired/location # Move dir to desired location.
require __DIR__ . '../PCrawl/vendor/autoload.php'; #in PHP
```

### TODO list

- Leverage guzzlehttp asynchronous support 

### Standards

```
PSR-12
PHPUnit tests 
```

