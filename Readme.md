### This is in development stage. 
```
PSR-12
PHPUnit tests 
```

## A PHP scraping library.
#### Some salient features. Some in TODO: 
- Flexible , fluent API with Method cascading design pattern
- Support multiple client - curl, guzzle. 
- Easily make more variants of the same clients, on the fly or by extending the existing clients. Variants can have different settings. Such as user-agents,
- Optional custom clients. Providing a lot more control over the underlying client.
- Modify Response body using reusable callback functions
- Debug Response using several different criteria's to determine if the request was successful. Criteria's can be reused and set on the fly.
- Quickly parse html pages using querypath (TODO)

#### Immediate TODO list
Exception handling  
Querypath parser
Function comments

#### Some More planned
Leverage guzzlehttp asynchronous support.

### Usage 
This package functions can be divided into parts:
* [Fetching a page](Usage/Fetching.md)  
* [Modifying the response body](Usage/Modify_Response.md)  
* [Debugging the response](Usage/Debugging_Response.md)  
* [Parsing the response body](Usage/Parse_Response.md)  

### Installation
Ideally be installed via composer. But that's some way off.
Explore manual installation? maybe unnecessary effort... TODO


### Full Example
* [Parsing the response body](Usage/full_example.php)  
