---
title: Request Object
description: Request Object Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Request Object

In every controller you will find a `request` and `response` object. The request object contains information on the request made and the response object represents what will be sent back to the client.

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
$keepAlive = $this->request->header('Connection');
```
You can also modify the request headers.

## Checking the accepts header

```php
public function view($id = null)
{
    if($this->request->accepts('application/json')){

    }
}
```

There is also a `acceptLanguage` which will return a list of languages that the request can accept.

## Session

The Session Component uses the session object which can be accessed from the request object at any time (unlike a Component which can  only be accessed from the controller).

```php
class ContactsController extends AppController {
  public function setUserId($id){
      $session = $this->request->session();
     $session->write('user_id',$id);
  }
}
```

## Reading values from cookies in the request

You can get the cookie object or read a value for a cookie from the request object.

```php
public function doSomething()
{
    $value = $this->request->cookie('key');
}
```