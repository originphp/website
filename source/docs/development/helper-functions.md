---
title: Helper Functions
description: Helper Functions for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Helper Functions


## Debugging & Backtracing

### Backtrace

Triggers a backtrace from this point.

```php
backtrace(); 
```

### Debug

Debugs output of a var or object when in debug mode.

```php
debug($results);
debug($html,$isHtml = true); // for displaying html in the browser
```

### Pr

A `print_r` wrapper to print a variable in human friendly format when in debug mode.

```php
pr($data);
```

### pj

Prints a variable in JSON pretty print when in debug mode

```php
pj($data);
```

## Working with Data

### Env

Gets an environment variable

```php
$data = env('REMOTE_ADDR');
$data = env('DB_USER','root'); // default value if not available
```

### Now

A shortcut for `date('Y-m-d H:i:s')`

```php
$now = now();
```

### H

 `htmlspecialchars` convenience function.

 ```php
 h($someData)
 ```

## Checking Modes

### isConsole

To find out if the current instance is being run in console mode

```php
$bool = isConsole();
```

### debugEnabled

To check if debug mode is enabled

## Path helpers

```php
$bool = debugEnabled();
```

### tmp_path

A handy function for working with paths in the `tmp` folder

```php 
tmp_path('data.json'); # /var/www/tmp/data.json
tmp_path('data/data.json'); # /var/www/tmp/data/data.json
```

### storage_path

A handy function for working with paths in the `storage` folder

```php
storage_path('contacts.csv'); # /var/www/storage/contacts.csv 
storage_path('csv/contacts.csv'); # /var/www/storage/csv/contacts.csv
```

### config_path

A handy function for working with paths in the `config` folder

```php
config_path('stripe.php'); # /var/www/config/stripe.php
config_path('locales/en_GB.php'); # /var/www/config/locales/en_GB.php
```