# PParsers

A DOM parser - querypath library is used. Both css selectors and xpaths can be used.

There are a lot of functions in this library that make it really easy to get the exact data. Even with complex layouts
its a breeze to get several rows of data

First we create a parser object with a html string.

```php
// Create a parser object
$parser = new PParserBase($body);
// optional: change the html  
$body1 = 'some html'
$parser->setResponse($body1);
```

querypath functions can be directly used:

```php
$parser->qp->find();
$parser->qp->children();
```

Or we have some convenience functions:

```php
$parser->find();
$parser->children();
$parser->xpath();
``` 

# PParserCommon

Convenience library to get common blocks like links and forms

```php
$parser = PParserCommon($body ?: $this->body);
$links = $parser->getAllLinks();
$images = $parser->getAllImages();
$forms = $parser->getAllFormInputDetails();
```

# Querypath usage

