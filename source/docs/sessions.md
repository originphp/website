---
title: Sessions Guide
description: Sessions Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Sessions

Sessions helps you identify users across and persist data for them across multiple requests. OriginPHP ships with two Session engines, one is a wrapper for native PHP sessions, and another one uses Redis directly to manage sessions (without the use of globals). You can also use the `SessionEngineInterface` to create your own session manager.

Sessions are configured by default to be bit more secure, based upon [OWASP recommendations](https://cheatsheetseries.owasp.org/cheatsheets/Session_Management_Cheat_Sheet.html).

To configure your sessions set `config/sessions.php`

```php
use Origin\Http\Session\Engine\PhpEngine;

return [
    'className' => PhpEngine::class,
    'name' => 'id', // generic name
    'idLength' => 32, // Must be at least 128 bits (16 bytes)
    'timeout' => 900 // Logout after 15 minutes of in activity
];
```

> The SessionMiddleware should be loaded before any other middleware, this will ensure that it will be the first to run and the last to be processed. For backwards compatability this will be added automatically in version 3.x

In `app/Http/Application.php` load the `SessionMiddleware`

```php
use Origin\Http\Middleware\SessionMiddleware;

protected function initialize(): void
{
    $this->loadMiddleware(SessionMiddleware::class);
}
```

You can access sessions by using the `SessionComponent`, `SessionHelper` or the `Session` object from the Request.
