---
title: Request Object
description: Request Object Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Request Object
The request object contains information on the current request that has been made, such as the URL, headers, cookies etc.

## Request Methods

### Getting the URL

To retrieve the full url including query string use `url` method, if you don't want the query string then pass false as an argument

```php
$url = $request->url(); // url: /contacts/view/100
$withQuery = $request->url(true);// url: /contacts/view/100?page=1
```

## Determining the request method

The request `method` will return a string such as POST, PUT etc. The is function will check against a string or an array of methods to see if it matches up.

```php
$method = $request->method();

if($this->request->is('post')){
    // do something
}
```

## Allowing certain method

You can also allow only certain HTTP request methods, this can be a string or an array of methods.

```php
public function delete($id = null)
{
    $this->request->allowMethod(['post', 'delete']);
    ...
}
```

## Request Headers

To get all headers:

```php
$headers =  $this->request->headers();
```

To get a header

```php
$keepAlive = $this->request->headers('Connection');
```

You can also modify the request headers.

```php
$this->request->headers('Accept-Encoding','gzip,deflate');
$this->request->headers('HTTP/1.0 404 Not Found',null); // Using no value
```

## Checking the accepts header

```php
public function view($id = null)
{
    if($this->request->accepts('application/json')){

    }
}
```

There is also a `acceptLanguage` which will return a list of languages that the request can accept.

## Request Type

The default request type is html, however if the accept header asks for json or xml or a extension of the same was provided, then the default format will be changed accordingly.

You can get or set

```php
$value = $this->request->type();
$this->request->type('xml');
```

> The request type does not change headers or server variables. The request type is used for rendering views and errors.

## Reading values from cookies in the request

To read cookie values from the request

```php
$all = $this->request->cookies();
$value = $this->request->cookies('key');
```

You can also change the values for the request

```php
$this->request->cookies('key','value');
```

## Getting the Session Object

If you need get the session object for the request, then you can do so. The session object is the backend for the `SessionHelper` and `SessionComponent`.

```php
$this->request->session()->write('key','value');
$value = $this->request->session()->read('key');
```