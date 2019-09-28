---
title: Request Object
description: Request Object Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Request Object
The request object contains information on the current request that has been made, such as the URL, headers, cookies etc.

## Request Methods

### Getting the URL and Path

To retrieve the full url including query string use `url` method, if you don't want the query string then pass false as an argument

```php
$url = $request->url(); // url: http://localhost:8000/contacts/view/100
$withQuery = $request->url(true);// url: http://localhost:8000/contacts/view/100?page=1
```

Getting the path only (without the scheme,host and port)

```php
$url = $request->path(); // url: /contacts/view/100
$withQuery = $request->path(true);// url: /contacts/view/100?page=1
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

You can also allow only certain HTTP request methods, this can be a string or an array of methods, if a method is used that is not in the list, it will throw an exception.

```php
public function delete($id = null)
{
    $this->request->allowMethod(['post', 'delete']);
    ...
}
```

### Getting the IP address of the client

To get the IP address
```php
$ip = $this->request->ip();
```

### Getting the Host name

To get the hostname

```php
$host = $this->request->host();
```

### Checking for SSL and Ajax

You can also check if the request is AJAX or using SSL

```php
$result = $this->request->ssl();
$result = $this->request->ajax();
```

## Request Headers

To get all headers:

```php
$headers = $this->request->headers();
```

To get a header

```php
$keepAlive = $this->request->headers('Connection');
```

You can also modify the request headers.

```php
$this->request->header('Accept-Encoding','gzip,deflate');
$this->request->header('Content-type: application/pdf');
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
$this->request->cookie('key','value');
```

## Getting the Session Object

If you need get the session object for the request, then you can do so. The session object is the backend for `SessionHelper` and `SessionComponent`.

```php
$this->request->session()->write('key','value');
$value = $this->request->session()->read('key');
```