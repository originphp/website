---
title: Response Object
description: Response Object Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Response Object
The response object contains information on the response that will be sent back to the client.

| Property                      | Definition                                                                            |
| ------------------------------|-------------------------------------------------------------------------------------- |
| status                        | This is the HTTP status code, e.g 200 for success or 404 for not found                |
| body                          | This is the string that is being sent to the view                                     |
| headers                       | These are the headers that will be sent                                               |
| contentType                   | The content type this could be html, json, csv etc                                    |

## Setting Custom Headers

You can set through the response object.

```php
$this->response->header('Accept-Language', 'en-us,en;q=0.5');
```

To get headers being set in the response

```php
$all = $this->response->headers();
$acceptLanguage = $this->response->headers('Accept-Language');
```

## Setting and getting the Content Type

```php
$type = $this->response->type();
```

```php
$this->response->type('application/vnd.ms-powerpoint');
```

## File Downloads

Sometimes you will need to send a file which is different from rendering a page. You can also force it to download the file
by setting `download` to true. The available options are `name`,`type` for content type and `download`. 

```php
$this->response->file('/tmp/transactions.pdf');
$this->response->file('/tmp/originphp.tmp',['name'=>'transactions.pdf']);
$this->response->file('/tmp/transactions.pdf',['download'=>true]);
```

## Cookie

You also write cookies using the response object instead of the `CookieComponent` or `CookieHelper`

```php
$this->response->cookie('key','value');
$this->response->cookie('key','value',['expires' =>'+7 days']);
$this->response->cookie('keyToDelete','',['expires' => '-60 minutes']); // to delete
```

> When writing cookies the values wont be available for reading until the next request, since they are only sent after everything has been rendered to the screen.