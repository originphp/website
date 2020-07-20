---
title: Maintenance Mode
description: Maintenance Mode for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Maintenance Mode

Sometimes you will need to carry out maintenance on your application, the maintenance mode temporarily disables
the web application and displays a screen that informs users that the application is down for maintenance.

When maintenance mode is enabled

- the web application will either 
    - throw a `MaintenanceModeException` with a 503 status code, this is carried out by the `MaintenanceModeMiddleware` (default behavior)
    - redirect user to the `public/maintenance.html` page
- The `queue:worker` command won't process any jobs
- The `mailbox:download` command wont download any emails
- The email piping server will store incoming emails to disk, and then will be imported next time an email comes in when not in maintenance mode

## Start Maintenance Mode

To start maintenance mode

```bash
$ bin/console maintenance:start
```

Options are:

- message: This is the a custom message that will be shown when the exception is thrown
- allow: Use this to set ip addresses that can still access the web application
- retry: Number of seconds to wait before retrying, this is added to the created time and then set in header `Retry-After`.

For example:

```bash
$ bin/console maintenance:start --message "Upgrading the database" --allow 192.168.1.120 --allow 192.168.1.140
```

By default a `MaintenceModeException` is thrown, however if you prefer to show a custom HTML page, then you can configure the Middleware to do this

edit your `app/Http/Application.php` where the Middleware is loaded

```php
$this->loadMiddleware(MaintenanceModeMiddleware::class, [
        'html' => true
    ]);
```

This will render `public/maintainence.html`

![HTML Maintenance Mode](/assets/images/maintenance-mode.png)

## End Maintenance Mode

Once you have finished carrying out any maintenance, run the `maintenance:end` command

```bash
$ bin/console maintenance:end
```
