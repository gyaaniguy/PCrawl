### This is in development stage. 
```
PSR-12
PHPunit tests 
```

## A PHP scraping library.
#### Some salient features . Many are in TODO: 
- Flexible , fluent API with Method cascading design pattern
- Quick on the fly changes to requests. Such as changing options lated to cookies, headers etc 
- Support multiple client - curl, guzzle.
- Easily create different curl 'clients', with different options. 
- Modify Response body using re-useable callback functions
- Debug Response using several different criteria's to determine if the request was successful
- Quickly parse html pages using querypath (TODO)

#### Some More planned
- asynchronous support with react-php. Hopefully this will happen. React php support may require large rewrites.
OR  
- we could leverage guzzlehttp asynchronous support. but in that case curl clients would be out of the picture, which would be very undesirable. 
- 

#### Immediate TODO list
Exception handling  
Querypath parser
Function comments
asynchronous attempt


### Usage 

Check Usage.md 