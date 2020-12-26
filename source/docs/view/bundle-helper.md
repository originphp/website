---
title: Bundle Helper
description: Bundle Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Bundle Helper

Once your application is in production you will want to bundle CSS and Javascript files together to reduce the number of requests that are made to your server. Remember that you will need to ensure that Apache or the webserver is configured to cache static files, see our guide on configuring [Apache to cache static files](/docs/development/deployment)

Create `config/bundle.php`

```php
return [
    'bundle.js' => [
        'jquery.min.js', // this will point to /js folder
        '/js/popper.min.js'
    ],
    'bundle.css' => [
        'application.css', // this will point to /css folder
        '/css/jquery-ui.css'
    ]
];
```

> The `BundleHelper` does not support plugin CSS or Javascript files, you should copy any files you need to your application public folder.

Load the `Helper` in your `ApplicationController`

```php
class ApplicationController extends Controller
{
    protected function initialize(): void
    {
        $this->loadHelper('Bundle');
    }
```

Options that you can pass when loading the helper are

- minify: default:true minifies the Javascript and CSS files
- cache: default:true results are cached using the `origin` cache settings. Set to false to regenerate on each request.
- jsPath: default: cache_js path for JS output
- cssPath: default: cache_css path for CSS output

Now in your `Layout` or `View` Files you can do this

```php
<?= $this->Bundle->css('bundle.css') ?>
<?= $this->Bundle->js('bundle.js') ?>
```

Thats it, its that simple. 

As results are cached, whenever you update your source code with `git pull` on the server, you need to clear the cache.

To reset the cache and force the bundles to be regenerated run the following command

```bash
$ bin/console cache:clear
```