---
title: Middleware
description: Middleware Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Middleware

Middleware is a convenient way to intercept HTTP requests before they reach your application, from there you can modify the request and response that is generated from the requests.

## Creating Middleware

If you need to use middleware for your app, doing so is straightforward, just create the file in `Middleware` folder and make sure both the name and class ends with `Middleware`, for example, the foo middleware would be `FooMiddleware`.

Using the generate command you can quickly create a new Middleware class.

```linux
$ bin/console generate middleware Foo
```

For more information on code generation see the [code generation guide](/docs/development/code-generation).

This is what it will look like

```php
namespace app\Http\Middleware;
use Origin\Http\Request;
use Origin\Http\Response;
use Origin\Http\Middleware\Middleware;

class FooMiddleware extends Middleware
{
    /**
     * Handles the request. This is run on all middlewares first.
     *
     * @param \Origin\Http\Request $request
     * @return void
     */
    public function handle(Request $request) : void
    {
        $request->data('foo','bar'); // Change the request data
    }
    
     /**
     * Processes the response. This is run on all middlewares after the
     * request has been handled.
     *
     * @param \Origin\Http\Request $request
     * @param \Origin\Http\Response $response
     * @return void
     */
    public function process(Request $request,Response $response) : void
    {
        $response->cookie('foo',$request->data('foo')); // Changes the response
    }
}
```

## Loading Middleware

To load the middleware, you need to call `loadMiddleware` in the initialize method of `app/Http/Application.php` file. When web requests are run, the middlewares will be run, first `handle` will be called by each middleware, to modify the request object, then once it has finished, each middleware will run the `process` method using the final modified request object that has been passed through all the middlewares.

```php
protected function initialize() : void
{
    $this->loadMiddleware('RequestModifier');
}
```

You can also load middlewares from plugin folders.

```php
protected function initialize() : void
{
    $this->loadMiddleware('MyPlugin.RequestModifier');
}
```

If you prefer to add the Middleware Object

```php
use App\Http\Middleware\RequestModifierMiddleware;
protected function initialize() : void
{
    $this->addMiddleware(new RequestModifierMiddleware());
}
```

## Callbacks

Middleware has `startup` and `shutdown` callbacks, which are called before and after Middleware has been invoked. 

## CSRF Protection Middleware

OriginPHP comes with the CSRF Protection Middleware which is enabled by default, when running the PHPUnit tests, the validation is disabled automatically.

How this works is, it creates a secure long token, then it inserts this into every form when you call Form::create, and a cookie is also created. When data is posted, the CSRF Protection Middleware, checks that the CSRF token is valid.

> If you are creating a form without the `FormHelper`, then you can call Form->csrf() to create the field manually.

The CSRF token is stored in the request parameters which you can access using the request object.

```php
$token = $this->request->params('csrfToken');
```

The CSRF Protection Middleware will also check the request headers for `X-CSRF-Token`, so you can tell libraries such as Jquery to send this automatically.

```js
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': '<?= $this->request->params('csrfToken') ?>'
    }
});
```

You can disable CSRF Protection for routes.

```js
Router::add('/api/:controller/:action/*',['type'=>'json','csrfProtection'=>false])
```

## AccessLog Middleware

This is a simple Middleware that creates an access log using the Apache Common LOG format, but using the logged in user id.

To the load the Middleware:

```php
protected function initialize() : void
{
    $this->loadMiddleware('AccessLog');
}
```

## Firewall Middleware

The Middleware can be used to block certain IPs or only allow certain IPs.

Create a `blacklist.php` or `whitelist.php` in your `config` directory, then load the Middleware.

The files should return an array like this 

```php
return [
    '80.80.80.12'
];
```

Whitelist will allow you restrict access to only certain IPs, and blacklist is used to block ips.

To the load the Middleware:

```php
protected function initialize() : void
{
    $this->loadMiddleware('Firewall');
}
```

## IDS (Intrusion Detection System) Middleware

This is lightweight but powerful application level IDS (Intrusion Detection System) to help you identify IP addresses that are trying to use SQL injection or XSS attacks on your web application. 

In your `app/Http/Application.php`

```php
protected function initialize() : void
{
    $this->loadMiddleware('Ids',[
        'level' => 3
    ]);
}
```

It also provides a rules engine for you to extend the rules if you want too. To use a different set or rules create a `config/rules.php` with the following keys. Note: if you use a rules file, then the default rules will be overwritten using the rules that you provide.

```php
return [
    [
        'name' => 'SQL Injection (paranoid)',
        'signature' => '/(\%27)|(\')|(?<!=)=(?!=)|(\%23)|(\#)|(\-\-)|(\%2D)/ix',
        'description' => 'Check for SQL specific meta-characters such as quote, equals comments #/-- and their hex equivalent',
        'level' => 3
    ],
];
```

It supports different levels, included are rules from levels 1-3. 3 being warnings that can catch anything, 1 should be almost certain and 2 in-between.



## Profiler Middleware

This Middleware keeps track of the memory usage and time it takes for each request. This is handy
for new application being deployed to locate memory leaks and long running requests.

This will produce something like this in  `/var/www/logs/profile.log`

`[2019-11-03 13:49:11] GET http://localhost:3000/bookmarks/add 0.0644s 934.87kb`


```php
protected function initialize() : void
{
    $this->loadMiddleware('Profiler');
}
```

## Throttle Middleware

You can restrict the rate of requests, block malicious requests and help mitigate DDOS attacks at the application level. Simply enable this middleware and by default it will block and ban IPs that make more than average 1 request per second, by checking over an average of 10 seconds, this allows for bursts and redirects etc.

> This checks requests wether or not they are valid, so an invalid url or missing favicon will trigger a second request.

```php
protected function initialize() : void
{
    $this->loadMiddleware('Throttle',[
        'limit' => 10,
        'seconds' => 10,
        'ban' => '+30 minutes'
    ]);
}
```

## Maintenance Mode Middleware

Maintenance mode is handled by this middleware and is enabled by default, see [Maintenance Mode](/docs/development/maintenance-mode).

To load the `MaintenanceModeMiddleware` which will throw a `MaintenanceModeException` if the application is in maintenance mode.

```php
protected function initialize() : void
{
    $this->loadMiddleware('MaintenanceMode');
}
```

If you prefer that the middleware render a custom html file, then you can set `html` to `true` and this will render the `maintence.html` in the `public` folder.

```php
protected function initialize() : void
{
    $this->loadMiddleware('MaintenanceMode', [
        'html' => true
    ]);
}
```

### Minify Middleware

Once your app is in production, you will probably want to minify the HTML, OriginPHP comes with the MinifyMiddleware which can handle this in the background for you.

These are the default options, which you can change

```php
protected function initialize() : void
{
    $this->loadMiddleware('Minify', [
        'conservativeCollapse' => true, // Ensures there is at least one space between tags
        'minifyJs' => true, // Minifies inline Javascript
        'minifyCss' => true // Minifies inline Styles
    ]);
}
```