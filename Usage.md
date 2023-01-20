#### This is tentatively final usage of some functions


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


PRequest returns a PResponse Object.
This object contains the response body, and other fields like response headers, http code. 
`TODO: add 'nodebug/light' mode to skip extra data like httpcode`
This object accepts callbacks to manipulate response body
```php
$pResponse = $pRequest->get('google.com');
$pResponse->modBody(['toabs','tidy']);
$body = $pResponse->getBody();
```
`TODO: add common modifications, like toabsurls`



