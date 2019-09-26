---
title: Configuration
description: Configuration Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Configuration

The configuration for your application can be found in the `config` folder. There you will find the configuration files for the application, database, SMTP email accounts, storage, cache and logs.

## Environment Settings

When you first create a project, rename the `.env.php.default` to `.env.php` and set your information there. This information is specific to your current installation.

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

By default OriginPHP will look for a `.env.php` file in the your `config` folder. However, if you need to use `.env` script, then in your `config/bootstrap.php` you can add the following code:

```php
use Origin\Core\DotEnv;
$dotEnv = new Origin\Core\DotEnv();
$dotEnv->load(CONFIG . DS . '.env');
```

Here is an example of a dotenv file.

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

If you need to load additional `.env` files, then you can use DotEnv.

```php
Use Origin\Core\DotEnv;
$dotenv = new DotEnv();
$dotEnv->load('/path/.env');
```