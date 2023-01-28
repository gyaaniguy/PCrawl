### This is tentatively final usage of some functions

## PRequest

We start things by creating a PRequest object 
-> set options, like the desired http client, cookies behaviour, custom headers etc
```php
$pRequest = PRequest([
    'user_agent'      => 'good_bot',
    'headers'         => ['header: value'],
    'tidy'            => true,
])
$pResponse = $pRequest->get('google.com');

```
Alternatively chain method pattern can be used.
```php
$pRequest = $pRequest->useCurl()
    ->setUserAgent("bad bot")
    ->setStrictHttps()
    ->enableCookies()
    ->setRedirects(2)
    ->setHeaders(['accept: /*/'])
    ->enableTidy();
```
We can also fetch the options from PRequest for reusability
```php
$opts = $pRequest->getOptions();
$anotherRequest = new PRequest($opts);
$anotherRequest->disableCookies()->get('google.com');
```

### Http Clients
Default is CurlClient class. A wrapper around 'curl'.
New clients can be creatd and used very easily. 
1. TODO:  guzzlehttp
2. Custom clients:  
   - Extend the CurlClient class. Set $defaultOptions=[] to set any curl options. See PRequestTest.php file for more examples.
```php
class OnlyHeadClient extends CurlClient
{
    public array $defaultOptions = [
        'custom_client_options' => [
            CURLOPT_NOBODY => 0,
        ],
        'user_agent'            => 'Only Head bot',
    ];
} 
$req = new PRequest();
$req->setClient(new OnlyHeadClient())->enableCookies();
$onlyHeadRes = $req->get('icanhazip.com');
```  
This is a  fairly useful feature as custom curl clients, one with different user-agents, or any other options can be created on the fly and passed around.

```php
$onlyHeadClient=new OnlyHeadClient();

$req
->setClient($onlyHeadClient)
->enableCookies();
->get('icanhazip.com');

$req->setClient(new CurlClient())
->setUserAgent("windows")
->disableCookies()
->get("site.com")

$req
->setClient($onlyHeadClient) // Sets 'defaultoptions' of this client (nobody), while keeping other options(useragent in this case) 
->get("site.com");
```


 - Alternatively make a new class that implements `InterfaceHttpClient` 

## PResponse
PRequest returns a PResponse Object.
This object contains the response body, and other fields like response headers, http code. 
`TODO: add 'nodebug/light' mode to skip extra data like httpcode`
This object accepts callbacks to manipulate response body
```php
$pResponse = $pRequest->get('google.com');
$pResponse->modBody(['toabs','tidy']);
$body      = $pResponse->getBody();
```
`TODO: add common modifications, like toabsurls`



