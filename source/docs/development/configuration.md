---
title: Configuration
description: Configuration Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Configuration

The configuration for your application can be found in the `config` folder. There you will find the configuration files for the application, database, SMTP email accounts, storage, cache and logs.

## Environment Settings

> As of 2.6, .env is used, and a cached version is created when not in debug mode, and this file is .env.php (this provides backwards compatibility).

When you first create a project with composer,it will rename the `.env.default` to `.env` and set your information there. This information is specific to your current installation. 

As you build your application then will be some custom variables that you use, make sure you always add these definitions to the `.env.default` template, since the `.env` file wont be stored in the git repository for security reasons. This way when you deploy your app to a new server, simply rename the template and fill out the details.

> If you make changes to the `.env` file, remember to clear the cached version which is `.env.php`.

## Config Class

Use the Config class to work with configuration values within your application.

```php
use Origin\Core\Config;
Config::write('key','value');
$value = Config::read('key');
Config::delete('key');
$bool Config::exists('key');
```


## DotEnv

By default OriginPHP will look for a `.env.php` file in the your `config` folder, this has been designed due to the overhead in parsing a traditional.env, but most frequently it not used beyond setting simple values. If one is not available, then it will look for `.env`, if its available it will parse it and cache it as `.env.php`.


Here is an example of a `.env` file.

```linux
GMAIL_USERNAME=foo@gmail.com
GMAIL_PASSWORD=secret
```

You can also add `export` in front of each line so you can source the file with bash.

```linux
export GMAIL_USERNAME=foo@gmail.com
export GMAIL_PASSWORD=secret
```

Then source it

```linux
$ source .env
```

Then you can access environment vars like this

```php
$username = env('GMAIL_USERNAME');
```

If you need to load additional `.env` files, then you can use `DotEnv` class.

```php
Use Origin\Core\DotEnv;
$dotenv = new DotEnv();
$dotEnv->load('/path/.env');
```