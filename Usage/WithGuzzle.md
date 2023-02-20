#### Guzzle Customizations and options

Its important the user has teh ability to utilize the power of guzzle while still being to use this library
So there are several ways in which it can be used


#### Use Guzzleclient . Modify options on the fly
```php
$goodBotClient = new GuzzleClient();
$goodBotClient->setRedirects(4)->setUserAgent("Good bot");
$req = new PRequest($client);
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
$req = new PRequest($goodBotClient);
$res = $req->get('google.com',['page_num' => 2]); // Fetches google.com?page_num=2 
```
#### Extend GuzzleCustom client. 
Allows setting options for the guzzle object directly. So you directly interact with internal guzzle object used by the library. PS: These clients do not have the set_ functions. So you have to set the options directly.
```php
class OnlyHeadGuzzleClient extends GuzzleCustomClient
{
    public array $clientOptions = [
        'headers' => [
            'head5' => 'value',
        ],
    ];
}
$req = new PRequest();
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
$req = new PRequest();
$req->setClient($guzzleCustom);

// Get
$res = $req->get('http-html-text/http-request-headers');
```
PS: In a real application all the clients could be created once in a location and then reused in different requests via `$request->setClient()`