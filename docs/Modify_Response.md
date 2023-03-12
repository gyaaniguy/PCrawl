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
