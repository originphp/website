---
title: Upgrade Guide
description: Upgrade Guide for 3.0
extends: _layouts.documentation
section: content
---
# Upgrade Guide

## Preparing for the Update

Version 3 is focused on upgrading to use PHP 7.3 as the minimum requirement, and PHPUnit 9, removing old deprecations and few small design changes.


The first thing to do is to update your app to the lastest 2.x version of the framework, this should update
to version `^2.8.1`

```bash
$ composer update
```

After upgrading your app, you will want to make sure that there are no deprecation warnings, these are shown when debug mode is enabled.


## Updating

Due to the various changes made during in version 2 to base application configuration, it is best to create
a new project and then move your code to this project.

```bash
$ composer create-project originphp/app app-v3
```

Once you have created the project copy the contents of your `app` and `tests` folder into the new project.

In your old project:

a) Check your `config` folder for changes you made from the default versions and move these settings over.

1. Check `config/app.php` or `config.application.php` (older versions) and move these configuration settings over.
2. Copy over your `.env.default` and `.env` file to the new project. In older version you might just have `.env.php` and `.env.php.default` which you will need to create `.env` versions of these in your new project.
3. Check your settings files such  `cache`, `database` and set

b) Copy over your files from `database` into the new project.

So the check list is this

- [ ] Copied over `.env` settings
- [ ] Copied over custom configurations
- [ ] Copied over contents from `database` which includes schema and migrations


## Breaking Changes

Check through below to see if you have used any of these features, if you have then you will need to make
some changes. 

### Elements

If you have used the `elements` in your `Views` then you will need to make some changes.

Copy the elements from `View/Element` to `View/Shared`.

You will also need to modify `$this->element(` references in your views to `$this->renderShared(`

If you have used `View\Exception\MissingElementException` then you will need change this to `View\Exception\MissingSharedViewException`

### onError and onSuccess

If you have used `onError` or `onSuccess` in either `Model`,`Mailbox` or `Job` then these are no longer
hooks, these are functions to register the callback.

Change your method from `onError` to `errorHandler` (or something else), then in `initialize` register the callback by adding `$this->onError('errorHandler');` - this is the same for `onSuccess`

### Model

`Model\Exception\NotFoundException` has been removed, so if you have used this then you will need to 
adjust to `Model\Exception\RecordNotFoundException`

### Mailbox

If you have called `Mail::$attachments` then change this to `Mail::attachments()`

If you have used `afterProcess` callback then change to use `onSuccess`, `afterProcess` is still there
but now it is called even if a mail bounces.

### BaseObject

The `BaseObject` class has been moved to the `Core` folder, so if you have used this then you will need to change the namespace.