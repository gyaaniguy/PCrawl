### This is tentatively final usage of some functions

## PRequest

Create PRequest object, fetch page.
```php
$pRequest = PRequest()
$pResponse = $pRequest->get('google.com');

```
Optionally pass a client object. Various options can be set on the client Object. Like cookies behaviour, custom headers etc
Alternatively chain method pattern can be used.
```php

$client = new CurlClient();
$client->setRedirects(4)->setUserAgent("Bro bot");
$req = new PRequest($client);
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
```


##### Custom Clients
Thin wrappers around the underlying curl and guzzle clients. Meant to used when you need control. Don't have set functions.
Extend the CurlCustomClient OR GuzzleCustomClient class. Set $customClientOptions=[] to set any curl options. See PRequestTest.php file for more examples.
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
$req = new PRequest();
$req->setClient(new OnlyHeadClient());
$onlyHeadRes = $req->get('icanhazip.com');
```  
This is a fairly useful feature as custom curl clients, one with different user-agents, or any other options can be created on the fly and passed around.

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

## Debug The Response with ResponseDebug
Set various conditions to analyze response and potentially find out why it failed to fetch the right page.
This can be used to detect:
- faults: incorrect response codes, being blocked by firewalls. Detecting 404 etc.
- unexpected outputs. No set-cookie header. unexpected JS redirects .

```php
$req = new PRequest();
$res = $req->enableCookies()->get($url);

//Create Response Debug Obj. Set some failiure conditions
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
