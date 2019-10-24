---
title: Upgrade Guide
description: Upgrade Guide for 2.0
extends: _layouts.documentation
section: content
---
# Upgrade Guide

## Welcome

> There is an Upgrade tool available, see below for more about this. Whilst there are a number of changes, its only the features that you have used which changed need your attention, apart from the main changes, many changes wont affect you.

Version 2 removes deprecated features and provides a more organized folder structure and makes it easier to work with given the number of folders that are now used. 

Other changes such as strict, return types, dropping public properties and other changes to take advantage of modern PHP features and best practices. 

`Model` and `Controller` callbacks have been redesigned to be registered and work with `Concerns` in a powerful way.

The bootstrap process has changed, the bootstrap process is carried out in your `config` folder.

The framework has been decoupled in various packages.

I have been working full time on the framework to get this where it is now, changes going forward from here should be slow, with a focus on improving code base, developing and testing with future PHP versions, bug and security fixes.

I will be happy to help you with upgrading if you contact me within the following weeks, just send me an email to `js@originphp.com`.

## Not Upgrading?

Firstly, if you don't want to upgrade to the new version then you will need to update your `composer.json` file, to ensure that when you run `composer update` it does not download version 2.x, the `composer.json` file from most versions of the framework will just update to the latest version automatically.

> Very early versions of the framework might trigger errors when upgrading to later versions

```json
  "require": {
    "php": "^7.2.0",
    "originphp/framework": "^1.30"
  },
  "require-dev": {
    "originphp/debug-plugin": "^1.3",
    "phpunit/phpunit": "^7.5"
  }
```

## Summary

Heres is a quick summary of the main changes which are in more detail below

1. Create a new project, this will setup the new bootstrap, add new files, folder structure a project dependencies.
2. Move your source into the new folder structure (which basically is splitting the console and web app)
3. controller hooks before/afterFilter have been renamed to startup/shutdown inline with rest of framework.
4. Model and Controller callbacks are now registered. For example in a model you do `$this->beforeSave('hashPassword');`
5. Behaviors have been removed and replaced with Concerns (which use traits)
6. Packages have been decoupled, anything in `Origin\Utility` will need to change to new namespace

If you have any problems or questions, send me an email or open an issue on github.

## Upgrade Tool

> Create a backup of your source code

The upgrade tool will

1. convert the old folder structure to the new structure and update USE statements
2. update Namespace changes
3. warn you of features that you are using that might have changed/removed or require action

What you will need to do (upgrade tool will warn you)

1. Adjust usage of Model or Controller callbacks (register them and rename the old callbacks), see below.
2. If you used the Email utility, need to migrate this to Mailer or adjust as stated below
3. Require any packages for your project that have been decoupled (you will be ad)
4. Your mailer templates will have to be manually moved and renamed as stated below
5. any public framework properties will need to be changed to protected, e.g Controller::$layout, Model::$table etc. This also affects Jobs and Mailer.
6. If you have created Behaviors, you will need to recreate them as Concerns (basically make them a trait - see below)
7. If you created middleware then rename the `startup` and `shutdown` to `handle` and `process`. 

Those are the main things, a few other changes have been made and are listed below, however the upgrade tool will warn you.

Its really important to make a backup of your source code, then the next step is make sure your application works up to version `1.33`, mainly handling any deprecation notices.

Create a new project

```linux
$ composer create-project originphp/app app-v2
```

Install the upgrade application

```linux
$ cd app-v2
$ composer require originphp/upgrade
```

Copy the contents of your `app` folder into the `app` folder (leave structure as is), here is an example:

```
$ cp -r ~/code/original-project/app/* ~/code/app-v2/app
```

Copy the contents of your `tests` folder into the `tests` folder (leave structure as is), here is an example:

```
$ cp -r ~/code/original-project/tests/* ~/code/app-v2/tests
```

Do a DRY run

```linux
$ bin/console upgrade --dry-run
```

This will show you what it will change automatically and possible issues that need to be looked
due to changes ( model callbacks, controller callbacks, composer dependencies, or public properties)


## Parent Class Changes

Parent classes such as `AppController` have been renamed to use the Application prefix, e.g. `ApplicationController`.

- AppController
- AppModel
- AppService
- AppMailer
- AppJob
- AppHelper

## Folder Structure Changes

HTTP and Console have been separated, as a result views for Http are in the HTTP folder and email templates are stored in the Mailer folder.

You will notice there is a new folder structure 

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

Moving the files to new folder structure will require references to these to be changed as below:

- `App\Command` change to `App\Console\Command`
- `App\Controller` change to `App\Http\View`
- `App\View` change to `App\Http\View`
- `App\Middleware` change to `App\Http\Middleware`

This is the same for the Origin namespace as well

- `Origin\Command` change to `Origin\Console\Command`
- `Origin\Controller` change to `Origin\Http\View`
- `Origin\View` change to `Origin\Http\View`

## Mailer Templates

Templates for Mailers are now stored in a different location and use a different filename structure

`/app/Mailer/Template/welcome_email.html.ctp`
`/app/Mailer/Template/welcome_email.text.ctp`

Layouts for mailers location have also changed

`app/Mailer/Layout/mailer.ctp`.

Create the default layout `app/Mailer/Layout/default.ctp`

```php
<!DOCTYPE html>
<html>
  <head>
    <meta content='text/html; charset=UTF-8' http-equiv='Content-Type' />
  </head>
  <body>
     <?= $this->content() ?>
  </body>
</html>
```

Adjust your `Mailers` to use the `default` layout, if you have previously used a layout.

```
class ApplicationMailer extends Mailer
{
  protected $layout = 'default';
}
```

## Class Location Changes

As a result of folder structure changes framework classes have been moved following the same structure

Framework Locations

- `Origin\Command\Command` changed to `Origin\Console\Command\Command`
- `Origin\Controller\Controller` changed to `Origin\Http\Controller\Controller`
- `Origin\Controller\Component\Component` changed to `Origin\Http\Component\Component`
- `Origin\View\View` changed to  `Origin\Http\View\View`
- `Origin\Http\Middleware` changed to `Origin\Http\Middleware\Middleware`


All HTTP exceptions for example:

- `Origin\Exception\NotFoundException` have been moved to the Http folder, for example `Origin\Http\Exception\NotFoundException`

These include BadRequest,Forbidden,HttpException,InternalError,MethodNotAllowed, ServiceUnavailable, NotImplemented.

- `Origin\Exception\InvalidArgumentException` moved to - `Origin\Core\Exception\InvalidArgumentException`
- `Origin\Exception\Exception` moved to - `Origin\Core\Exception\Exception`

## Controller Callbacks

`beforeFilter` and `afterFilter` have been renamed to `startup` and `shutdown` respectively, these do not need to be registered. 

You can register additional callbacks which are called between these, using the following methods

```php
$this->beforeAction('checkRequest');
$this->afterAction('cleanResponse');
$this->beforeRedirect('logSomething');
$this->beforeRender('cacheView');
```

If you have used `beforeRedirect` or `beforeRender` rename these, and then register as above.

## Connection

`Model::$datasource` has been renamed to `Model::$connection`, if you have used this feature then you will need
change this.

Console commands with the `datasource` option has been renamed to `connection` or short option `c`, if you are using these in scripts or cron jobs then you will need to adjust.

## Model Callbacks

Model callbacks have been changed completely, the return types and and the arguments are both the entity and an `ArrayObject` with the option. With the exception, `beforeFind` and `afterfind`. The `afterfind` first argument is always a `Origin\Model\Collection`, even if you are doing find first.

Callbacks are now registered but use the same name for registration so any callbacks that have used you will need to rename the existing callback, by adding a callbacks suffix, then just defining it. For example:

```php
protected function initialize(array $config)
{
  $this->beforeFind('beforeFindCallback');
}

protected function beforeFindCallback(ArrayObject $options) : bool
{
    return true;
}
```

See [Callbacks guide](https://www.originphp.com/docs/model/callbacks/) for more information.

## Configure Class

The `Configure` class has been renamed to `Config`, depending upon the version you used to create your project you may have already been using this. Find `Configure::` references.

## Return Types

`Initialize` and `execute` methods have a return type of void, except in Service Objects where the execute method should return nothing or a `Result` object.

In your PHPUnit tests `startup` and `shutdown` also have a return type of void.

## Public Properties

All public properties have been changed to protected, including `Mailer`,`Job`, `Schema` which previously used public properties for configuration.

## Middleware

If you have not created any middleware, then you can skip this section.

Previously Middleware used `startup` and `shutdown` as aliases for `invoke` and `process`, this has been changed, and startup and shutdown now callbacks inline with rest of framework. Rename your Middleware `startup` to `invoke` and `shutdown` to `process`.

## Behaviors

Behaviors have been removed and replaced with `Concerns`, but don't be concerned they work the same way just that now they use traits, so now we just use less magic.

You can create the base like this and move your code (callbacks work the same as in models see this page for info)
```
$ bin/console generate concern_model NameOfConcern
```

### Behavior References

In your models any references to old framework behaviors need to be adjusted to their equivalent as a `Concern`. 

```php
namespace App\Model;

use Origin\Model\Model;
use Origin\Model\Concern\Delocalizable;
use Origin\Model\Concern\Timestampable;

class ApplicationModel extends Model
{
    use Timestampable,Delocalizable;
    protected function initialize(array $config): void
    {
    }
}
```

### Custom Behaviors

If you have created any custom Behaviors, these will be need to converted to `Concerns`, the code will be almost identical, except you don't need to reference `$this->model` since a `Concern` is a trait.

## Removed Features

All previous deprecated features have been removed, the following functions have also been removed

- Behaviors
- Text::random
- uuid
- left
- right
- contains
- replace

## Other Changes

- Security::uid returns base 62 string with a length of 16. e.g `O64cjBxfz2JPhyCQ`

## PHPUnit

The framework has been updated to work with PHPUnit 8.0+.

## Migations

The [migration schema](https://github.com/originphp/app/blob/master/database/migrations.php) has changed, version is now a `bigint` field. You will need to dump your schema and then delete the migrations table and reimport using the new one

```linux
$ bin/console db:schema:load migrations
```

## Cookies

When writing cookies, setting the expiration is done via the options array, now the third argument, in any class that allows you to write cookies.

```php
$this->response->cookie('key','value',['expires'=>'+ 10 days']);
$this->Cookie->write('key','value',['expires'=>'+ 10 days']);
```

## Locale Definition Files

Your locale files in `config/locales` need to be regenerated, the format has been changed to PHP from YAML.

```linux
$ bin/console locale:generate en_UK en_US
```

## Initialization Trait

If you have used the Initialization trait, then you will need to change the initialization method name, which now must include the prefix initialize

```php
trait  Foo
{
  protected function initializeFoo()
  {

  }
}
```

## Email Utility

If you used the email utility, the functionality of this has been reduced to just sending emails, so it no longer uses templates or creates a text version automatically, also the default type is `text`.  You should rewrite any usage of this to use the [OriginPHP Mailer](/docs/mailer.md) or if you can't use this for some reasons, then make sure

1. set `$email->format('both')` since the default has changed to text.
2. if you are not supplying a text version and was relying on automatically creating one, then use the [Html](/docs/utility\html) tool to convert the HTML body into text e.g. `Html::toText($message)`

## Decoupling

Packages have been decoupled from the framework and are available as composer packages and also as a result the namespaces changed.

These packages are installed as part of the framework, so you only need to change references from `Origin\Utility`

- Inflector is now a composer package and under a different namespace `Origin\Inflector`
- Security is now a composer package and under a different namespace `Origin\Security`
- Yaml is now a composer package and under a different namespace `Origin\Yaml`
- Html is now a composer package and under a different namespace `Origin\Html`
- Email is now a composer package and under a different namespace `Origin\Email`

The following packages are not installed by default but can be installed through composer

For example 

```linux
$ composer require origin\collection
```

- Collection is now a composer `originphp/collection` package and under a different namespace `Origin\Collection`
- CSV is now a composer package `originphp/csv` and under a different namespace `Origin\Csv`
- DOM is now a composer package `originphp/dom` and under a different namespace `Origin\Dom`
- File and Folder are now in a composer package (`originphp/filesystem`) and under a different namespace `Origin\Filesystem`
- Text is now a composer package `originphp/text` and under a different namespace `Origin\Text`
- Markdown is now a composer package `originphp/markdown` and under a different namespace `Origin\Markdown`
- Http is now a composer package `originphp/http-client` and under a different namespace `Origin\HttpClient`


## Note

Log out of your existing application and delete any cookies to prevent possible errors.