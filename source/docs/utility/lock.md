---
title: Lock
description: Lock for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Lock

The Lock object is simple and can be used to ensure that only one process is doing something at a given time.

## Installation

Lock is part of the framework. but if you want to use this in your other projects you can install the package using composer.

```linux
$ composer require originphp/lock
```

## Usage

Simply create the Lock object using a name to represent the resource. When lock is acquired it will create a file in the system temporary directory, e.g. `/tmp/pdf-generate.lock`.

By default when you acquire a lock, it is blocking by default, which means that it will wait until it can get exclusive
access.

```php
use Origin\Lock\Lock;

$lock = new Lock('pdf-generate');

if($lock->acquire()){
    // do something

    $lock->release(); // release the lock
}
```

To use a non-blocking lock, if it is not able to acquire an exclusive lock then it will return `false`.

```php
$lock->acquire(false);
```

Locks will be automatically released, if they have not been already when the lock object is destructed. You can disable this behavior when construcing the `Lock` object by using the `autoRelease` option.

```php
$lock = new Lock('pdf-generate',[
    'autoRelease' => false
]);
```
