---
title: Storage Guide
description: Storage Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Storage

The Storage utility provides an easy way to access different types of storages from local disk,FTP, SFTP and Amazon S3 (coming soon). Its a unified approach for working with different storages.

The default configuration for Storage is the local storage engine,which stores data on the disc in the storage folder. You can configure different types of storages in the `config/storage.php` file.

## Storage Engines

### Local

The local storage simply works with data from the drive.

```php
Storage::config('default', [
    'engine' => 'Local',
    'path' => '/var/www/storage'
     ]);
```

### FTP

Then you need to configure this

```php
Storage::config('default', [
    'engine' => 'Ftp',
    'host' => 'example.com',
    'port' => 21,
    'username' => 'james',
    'password' => 'secret,
    'ssl' => false
     ]);
```

options for configuring FTP include:

- host: the hostname or ip address
- port: the port number. default 21
- username: the ftp username
- password: the ftp password
- timeout: default 10 seconds
- passive: deafult false
- root: the root folder of the storage within your ftp account

### SFTP

To use the SFTP storage you need to install `phpseclib` library.

```linux
$ composer require phpseclib/phpseclib
```

Then you need to configure this

```php
Storage::config('default', [
    'engine' => 'Sftp',
    'host' => 'example.com',
    'port' => 22,
    'username' => 'james',
    'password' => 'secret
     ]);
```

If you use want to use a private key to login, you can either provide the filename with the full path or the contents of the private key itself.


```php
Storage::config('default', [
    'engine' => 'Sftp',
    'host' => 'example.com',
    'port' => 22,
    'username' => 'james',
    'privateKey' => '/var/www/config/id_rsa'
     ]);
```

If your private key requires a password then you can provide that as well. See the [How to setup SSH keys ](https://linuxize.com/post/how-to-set-up-ssh-keys-on-ubuntu-1804/) tutorial for more information.

options for configuring SFTP include:

- host: the hostname or ip address
- port: the port number. default 22
- username: the ssh account username
- password: the ssh account password
- timeout: default 10 seconds
- root: the root folder of the storage. e.g. /home/user/sub_folder
- privateKey: either the private key for the account or the filename where the private key can be loaded from

## Using Storage

The Storage utility always uses the default storage unless you tell it otherwise.

### Writing To Storage

```php
use Origin\Utility\Storage;
Storage::write('test.txt','hello world!');
```

You can also write to folders directly.

```php
Storage::write('my_folder/test.txt','hello world!');
```

### Reading From Storage

```php
use Origin\Utility\Storage;
$contents = Storage::read('my_folder/test.txt');
```

### Deleting From Storage

```php
Storage::delete('my_folder/test.txt');
```

You can also delete a folder and all its contents.

```php
Storage::delete('my_folder');
```

### Listing Storage Contents

To list the files on the storage

```php
use Origin\Utility\Storage;
$allFiles = Storage::list();
```

Storage contents are listed recursively and it will provide you with an array of arrays. Each file has its own array.

```php

// Will look like this
[
    'name' => 'my_folder/test.txt',
    'size' => 1024,
    'timestamp' => 1559692998
];

```

If you just want the files of particular folder, then it will list all files recursively under that folder.

```php
use Origin\Utility\Storage;
$files = Storage::list('my_folder');
```

### Working with Multiple Storages

To work with multiple storages, tell it which configuration to use before you use the read, write, delete and list methods.

```php
use Origin\Utility\Storage;
Storage::use('ftp');
```

> If you are working with multiple storages then always call `use` before you use the storage, including the default.

Alternatively you can work with any storage engine directly.

```php
$sftp = Storage::engine('sftp');
$sftp->write('keys/somefile',file_get_contents($filename));
$data = $sftp->read('keys/somefile');
```