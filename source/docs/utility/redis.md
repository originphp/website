---
title: Redis
description: Redis for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Redis

Redis an open source in memory advanced key-value store, the Redis utilility makes it easy work.

## Installation

This is alreaady part of the framework, however if you want to use this package outside the framework you install with composer.

```bash
$ composer require originphp/redis
```

## Configuration

> If you don't provide a configuration, the default connection will be setup for you automatically using the default host and port.

```php
use Origin\Redis;

Redis::config([
    'default' => [
        'host' => env('REDIS_HOST'),
        'port' => (int) env('REDIS_PORT'),
    ]
]);
```

The following options are available for each configuration

- host: default:127.0.0.1 hostname
- port: default:6379 port number
- password: if you are using a password
- path: if you are using a socket
- timeout: set the default timeout
- database: database number (integer)
- prefix: a prefix for this connection
- persistent: default:false wether the connection is persistent. You can set to `true` or use a `string` to identify the name.

By default the `Redis` class uses the `default` connection.

```php
// writing
Redis::set('key','value');
Redis::set('key','value', ['duration' => 10]); // how long it lasts

// getting

$value = Redis::get('key');
$value = Redis::get('unkown','not-found');

// Check a key exists
$bool = Redis::exists('key');

// Delete
Redis::delete('key');

// to get a list of keys
$keys = Redis::keys();


// increment and decrement
Redis::increment('counter');
Redis::increment('counter', 2);
Redis::decrement('counter');
Redis::decrement('counter', 2);

// to flush all items in the current redis database
Redis::flush();

$client = Redis::client(); // get the PHP extension client if you need more functions.

```

If you are using multiple connections, you can get a specific connection instance with the same methods.

```php
$connection = Redis::connection('other');
$connection->set('foo', 'bar');
```
