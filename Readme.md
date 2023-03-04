## This is in alpha stage.

# PCrawl

PCrawl is a PHP library for crawling and scraping web pages.   
It supports multiple clients: curl, guzzle. Options to debug, modify and parse responses.

## Features

- Rapidly create custom clients. Fluently make changes to clients and client options, with method chaining.
- Responses can be modified using reusable callback functions. Modification, debugger objects can be swapped on the fly
  and reused.
- Debug Responses using different criterias - httpcode, regex etc.
- Fluent API. Different debugger, clients and response objects can be be changed on the fly !

### Full Example

Search a government database website for different keywords and make a list of all the datasets available. Do some
pagination

- Setup up some clients

```php
// simple clients.
$ch = new PCurlClient();
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

- Lets make some debugger classes

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
// Start some bad fetching

$url = "http://www.whatsmyua.info";
$res = $req->setClient($uptightNoRedirectClient);
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

### Detailed Usage

Usage of functions can be divided into parts:

* [Fetching a page](Usage/Fetching.md)
* [Modifying the response body](Usage/Modify_Response.md)
* [Debugging the response](Usage/Debugging_Response.md)
* [Parsing the response body](Usage/Parse_Response.md)

## Installation

via github:  
Clone this repo. Run composer update. Move dir to desired location. Included the autoload.php file in your project.

```
git clone git@github.com:gyaaniguy/PCrawl.git
cd PCrawl
composer update
mv ../PCrawl /desired/location

//In php code:
require __DIR__ . '../PCrawl/vendor/autoload.php';
```

via composer:
todo

### TODO list

Parser
Leverage guzzlehttp asynchronous support.
composer support

### Standards

```
PSR-12
PHPUnit tests 
```

