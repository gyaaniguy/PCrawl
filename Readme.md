### This is in development stage. 
```
PSR-12
PHPUnit tests 
```

## A PHP scraping library.
#### Some salient features. Some in TODO: 
- Flexible, fluent API with Method cascading design pattern
- Support multiple client - curl, guzzle. 
- Easily make variants of clients, on the fly or by extending existing clients. Variants can have different settings. Such as user-agents,
- Custom clients. Thin wrapper around curl, guzzle. So you are not restricted by library provided functions.
- Modify Responses using reusable callback functions
- Debug Responses using different criteria's. Determine  failiure. Criteria's can be reused and set on the fly.
- Quickly parse html pages using querypath (TODO)

#### Immediate TODO list
Querypath parser

#### Future TODO list
Leverage guzzlehttp asynchronous support.


### Full Example
* [Full example](Usage/full_example.php)

### Usage 
This package functions can be divided into parts:
* [Fetching a page](Usage/Fetching.md)  
* [Modifying the response body](Usage/Modify_Response.md)  
* [Debugging the response](Usage/Debugging_Response.md)  
* [Parsing the response body](Usage/Parse_Response.md)  

### Installation
via github:
Clone this repo. Run composer update. Move dir to desired location. Included the autoload.php file in your project.
```php
require __DIR__ . '../PCrawl/vendor/autoload.php';

```

via composer: 
todo



