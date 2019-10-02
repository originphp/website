---
title: Upgrade Guide
description: Upgrade Guide for 2.0
extends: _layouts.documentation
section: content
---
# Upgrade Guide

Version 2 removes deprecated features and provides a more organized folder structure and makes it easier to work with given the number of folders that are now used. I been working full time on the framework to get this where it is now, changes going forward from here should be slow, with a focus on improving code base, developing and testing with future PHP versions, bug and security fixes.

## Front Controller

The front controller `public/index.php` changed to easily allow for changes to be carried out in the framework, if needed. See [Github](https://github.com/originphp/app/blob/master/public/index.php).

## Bootstrap

The loading of configuration files has been moved from the framework bootstrap to the application bootstrap `config/bootstrap.php`.

For example

```php
include 'application.php';
include 'log.php';
include 'cache.php';
include 'database.php';
include 'storage.php';
include 'email.php';
include 'queue.php';
```

## Parent Class Changes

Parent classes such as `AppController` have been renamed to use the Application prefix, e.g. `ApplicationController`.

- AppController
- AppModel
- AppService
- AppMailer
- AppJob
- AppHelper

Console now introduces its own Application.php, this will allow for adding shared features and configuration to console
commands in the future.

```php
// app/Console/Application.php
<?php
namespace App\Console;
use Origin\Console\BaseApplication;
class Application extends BaseApplication
{
}
```

## Folder Structure Changes

HTTP and Console have been separated, as a result views for Http are in the HTTP folder and email templates are stored in the Mailer folder.

```
-- app
    |-- Console
    |   |-- Command
    |-- Http
    |   |-- Controller
    |   |-- Middleware
    |   |-- View
    |-- Job
    |-- Mailer
    |   |-- Template
    |   |-- Layout
    |-- Model
```

## Mailer Templates

Templates for Mailers are now stored in a different location and use a different filename structure

`/app/Mailer/Template/welcome_email.html.ctp`
`/app/Mailer/Template/welcome_email.text.ctp`

Layouts for mailers location have also changed

`app/Mailer/Layout/mailer.ctp`

## Class Location Changes

As a result of folder structure changes framework classes have been moved following the same structure

For example
`Origin\Controller\Controller` is now `Origin\Http\Controller\Controller`;

This affects the following

- Controllers
- Components
- Middlewares
- Views
- Commands
- Exception

Http exceptions such as `NotFoundException` can now be found in `Origin\Http\Exception`. 
Previously the Middleware class was in `Http`, now it is `Http\Middleware`.

## Controller Callbacks

`beforeFilter` and `afterFilter` have been renamed to `beforeAction` and `afterAction` respectively.

## Connection

`Model::$datasource` has been renamed to `Model::$connection`, if you have used this feature then you will need
change this.

Console commands with the `datasource` option has been renamed to `connection` or short option `c`, if you are using these in scripts or cron jobs then you will need to adjust.

## Model Callbacks

Model callbacks have been changed completely, before callbacks must return true to continue, and the arguments are both the entity and an `ArrayObject` with the option. With the exception, before and after find.

After find now always returns a collection even if you are doing a find first.

See [Callbacks guide](https://www.originphp.com/docs/model/callbacks/) for more information.

## Configure Class

The `Configure` class has been renamed to `Config`, depending upon the version you used to create your project you may have already been using this. You should your check your `config/application.php` file.

## Return Types

`Initialize` and `execute` methods have a return type of void.

## Removed Features

All previous deprecated features have been removed, the following functions have also been removed

- Text::random
- uuid
- left
- right
- contains
- replace

## Other Changes

- Security::uid returns base 62 string with a length of 15. e.g 64cjBxfz2JPhyCQ

## PHPUnit

The framework has been updated to work with PHPunit 8.3+. 
