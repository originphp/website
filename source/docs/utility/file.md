---
title: File Utility
description: File Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# File

The file utility wraps some important functions in an easy to use and predictable way.

## Installation

> Projects created after 15th August will have these installed by default.

To install this package use Composer. 

```linux
$ composer require originphp/filesystem
```

To use the File utility add the following to the top of your file.

```php
use Origin\Filesystem\File
```

## Info

To get information about a file

```php
$info = File::info('/var/www/config/insurance.csv');
```

Which will return this

```
Array
(
    [name] => insurance.csv
    [path] => /var/www/config
    [extension] => csv
    [type] => text/plain
    [size] => 1878
    [timestamp] => 1560067334
)
```

## Read

To read a file

```php
$contents = File::read('/path/somefile');
```

## Write

To write to a file


```php
File::write('/path/somefile','data goes here');
```

### Append

To append contents to a file

```php
File::append('/path/somefile','and here.');
```

## Delete

To delete a file

```php
File::delete('/path/somefile');
```

## Exists

To check if file exists

```php
$result = File::exists('/path/somefile');
```

## Tmp

When needing to work with temporary files, use tmp, this will create the file, put the contents and return to you the name of the file with path.

```php
$tmpFile = File::tmp('Some temp data');
```

## Copy

To copy a file

```php
File::copy('/path/somefile','somefile-backup');
File::copy('/path/somefile','/another_path/somefile');
```

## Rename

To rename a file

```php
File::rename('/path/somefile','new_name');
```

## Move

To move a file

```php
File::move('/path/somefile','/another_path/somefile');
```

## Permissions

### Get Permissions

To get the permissions of a file
01
```php
$permissions = File::perms('/path/somefile'); // returns 0744
```

### Changing Permissions (chmod)

To change the permissions of a file.

```php
File::chmod('/path/somefile','www-data');
```

### Getting the owner of the file

```php
$owner = File::owner('/path/somefile'); // returns root
```

### Changing Ownership (chown)

To change the ownership of a file

```php
File::chown('/path/somefile','www-data');
```

### Getting the group

To get the group that the file belongs to.

```php
$group = File::group('/path/somefile'); // returns root
```

### Changing Group (chgrp)

To change the group that the file belongs to.

```php
File::chgrp('/path/somefile','www-data');
```