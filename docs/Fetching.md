# Fetching Guide

## Fetching a Page

Create PRequest object, fetch page.

```php
$request = Request()
$response = $request->get('google.com');
```

Optionally pass a client object. Various options can be set on the client Object. Like cookies behaviour, custom headers
etc
Alternatively chain method pattern can be used.

```php

$client = new CurlClient();
$client->setRedirects(4)->setUserAgent("Bro bot");
$req = new Request($client);
// $req->setClient($client); // Alternative
$res = $req->get('google.com');
```

### Http Clients

Default is GuzzleClient.
New clients can be created easily. Or existing clients behaviour and setting can be modified on the fly.

##### On The fly

Not available with CustomClients but only with GuzzleClient and CurlClient.

```php
$broBot = $client->setRedirects(4)->setUserAgent("Bro bot");
```

##### Extended clients

```php
class AddPageNumClient extends CurlClient
{
    public function get(string $url, array $options = []): Response
    {
        if (!empty($options['page_num'])) {
                $url = $url."?page=".$options['page_num']; 
                return parent::get($url);
            }
        } 
        return parent::get($url,$options);
    }
}
```

##### Custom Clients

Thin wrappers around the underlying curl and guzzle clients. Meant to used when you need control. Don't have set
functions.
Extend the CurlCustomClient OR GuzzleCustomClient class. Set $customClientOptions=[] to set any curl options. See
PRequestTest.php file for more examples.

```php
class OnlyHeadClient extends CurlCustomClient
{
    public array $customClientOptions = [
        'custom_client_options' => [
            CURLOPT_NOBODY => 0,
            CURLOPT_HEADER => 1,
        ],
        'user_agent'            => 'Only Head bot',
    ];
} 
$req = new Request();
$req->setClient(new OnlyHeadClient());
$onlyHeadRes = $req->get('icanhazip.com');
```  

## PResponse

PRequest returns a PResponse Object.
This object contains the response body, and other fields like response headers, http code.
`TODO: add 'nodebug/light' mode to skip extra data like httpcode`
This object accepts callbacks to manipulate response body

```php
$response = $request->get('google.com');
$response->modBody(['toabs','tidy']);
$body      = $response->getBody();
```

`TODO: add common modifications, like toabsurls`

## Debug The Response with ResponseDebug

Set various conditions to analyze response and potentially find out why it failed to fetch the right page.
This can be used to detect:

- faults: incorrect response codes, being blocked by firewalls. Detecting 404 etc.
- unexpected outputs. No set-cookie header. unexpected JS redirects .

```php
$req = new Request();
$res = $req->enableCookies()->get($url);

//Create Response Debug Obj. Set some failure conditions
$debug = new ResponseDebug();
$debug->setGoodHttpCode(200)
    ->setbadStrings(["blocked"]) 
    ->setgoodStrings(["</html>"])  // If this is not found, response is considered failed.
    ->setGoodRegex(['/\d\d\d\d\d/'])
    ->setContainExpectedHeaders(['set-cookie: ','content-type: application/json']);
 
$debug->setResponse($res);
if ($debug->isFail()){
  $failAr = $debug->getFailDetail();
  // The ->setbadStrings(["blocked"]) condition is true. 
  if (isset($failAr['bad_string']) && stristr($failAr['bad_string'],'blocked')){
      //Modify req to use expensive proxy and fetch again.
      $res = $req->setProxy($priceProxy)->get($url);
  }
}
```

#### Guzzle Customizations and options

Its important the user has teh ability to utilize the power of guzzle while still being to use this library
So there are several ways in which it can be used

#### Use Guzzleclient . Modify options on the fly

```php
$goodBotClient = new GuzzleClient();
$goodBotClient->setRedirects(4)->setUserAgent("Good bot");
$req = new Request($client);
// $req->setClient($client); // Alternative
$res = $req->get('google.com');
```

#### Extend Guzzleclient

```php

class AddPageNumClient extends GuzzleClient
{
    public function get(string $url, array $options = []): PResponse
    {
        if (!empty($options['page_num'])) {
                $url = $url."?page=".$options['page_num']; 
                return parent::get($url);
            }
        } 
        return parent::get($url,$options);
    }
}

$goodBotClient = new AddPageNumClient();
$req = new Request($goodBotClient);
$res = $req->get('google.com',['page_num' => 2]); // Fetches google.com?page_num=2 
```

#### Extend GuzzleCustom client.

Allows setting options for the guzzle object directly. So you directly interact with internal guzzle object used by the
library. PS: These clients do not have the set_ functions. So you have to set the options directly.

```php
class OnlyHeadGuzzleClient extends GuzzleCustomClient
{
    public array $clientOptions = [
        'headers' => [
            'head5' => 'value',
        ],
    ];
}
$req = new Request();
$req->setClient(new OnlyHeadGuzzleClient());
$onlyHeadRes = $req->get('https://manytools.org/http-html-text/http-request-headers');
```

#### Use your own guzzle client.

Create your own guzzle client, then pass it to the library

```php
// Create a regular guzzle client
$myGuzzle = new /Guzzle/Client(['base_uri' => 'https://manytools.org/', 'headers' => ['User-Agent' => "raw guzzle"]]);

// Create a GuzzleCustomClient
$guzzleCustom = new GuzzleCustomClient();
$guzzleCustom->setRawClient($myGuzzle);

// Attach the customClient to a request
$req = new Request();
$req->setClient($guzzleCustom);

// Get
$res = $req->get('http-html-text/http-request-headers');
```

PS: In a real application all the clients could be created once in a location and then reused in different requests
via `$request->setClient()`