---
title: Configuration
description: Configuration Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Configuration

The configuration for your application can be found in the `config` folder. There you will find the configuration files for the database, SMTP email accounts, storage and for the server specific installation.

## Database

Save a copy of `database.php.default` to `database.php` and update the database name,username and password.

## Email Accounts (SMTP)

Save a copy of `email.php.default` to `email.php` and update database name,username and password.

## Storage

Save a copy of `storage.php.default` to `storage.php` and fill in the details.

## Server

The `server.php` file is the configuration specific to the installation. This is handy when your application might use different settings for different installations (e.g production, development, staging). Examples might include debug levels, log or domain information, api keys (non public projects).

> Update server.php.template to include the default configuration parameters but without sensitive information such as usernames, passwords or api keys. So when you deploy or reinstall your application, save a copy of the template and fill out the values.

### DotEnv

You can also `.env` files for your configuration needs. When your application is run either a HTTP request or through the command line, OriginPHP will look for `.env` in your applications config folder.

Here is an example

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