####  

This is a quick fluid way of settings various criterias to analyze a response object.

These can be used to narrow down why a request failed. Whether the login failed, or if the http codes or headers matched
to what we were expecting.

### Usage

Make different criteria's:

```php
$loggedInCriteria = new PResponseDebug();
$loggedInCriteria->setMustExistRegex(['/Logged In/']); 
$loggedInCriteria->setMustNotExistRegex(['/cloudflare/i']); 
$loggedInCriteria->setResponse($res);

$fourHundredsDetector = new PResponseDebug();
$fourHundredsDetector->setMustNotExistHttpCodes([400,404]);
```

Pass any response object/s to different criteria's for seamless checking..

```php
$res = $req->get('https://site.com');
if ($fourHundredsDetector->setResponse($res)->isFail())){
    var_dump($fourHundredsDetector->getFailDetail());
}
elseif($loggedInCriteria->setResponse($res)->isFail()){
    var_dump($loggedInCriteria->getFailDetail());
}
```

There are more criteria's to set. See the class/tests for more details.  
Will add api docs later. Famous last words.
