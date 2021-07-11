---
title: SSH
description: SSH for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# SSH

SSH class is a wrapper for the SSH2 PHP extension, it allows you to execute commands via SSH as well as send and receive files via SCP.

## Installation

SSH is part of the framework. but if you want to use this in your other projects you can install the package using composer.

```linux
$ composer require originphp/ssh
```

To install the `ssh2` extension,

```
$ apt install php-ssh2
```

## Usage

First create the SSH object with the authentication details

### Password based Authentication

```php
use use Origin\Ssh\Ssh;
$ssh = new Ssh([
    'host' => '192.168.1.100',
    'username' => 'yoda',
    'password' => 'secret',
]);
```

### Public Key Authentication

If you want to use public and private key pairs then you need to be aware of two things:

1. The public private key needs to be in PKCS1 format, e.g. BEGIN RSA PRIVATE KEY, if not you will not be able to login
2. You can't use keys encrypted with a password due to a bug which is caused when libssh2 is compiled with libgcrypt (most systems), unless you recompile libssh2 with openssh

Therefore, when using public and private key pairs, do not set a password, when prompted during the key generation process simply press enter.

To generate a public private key pair in PKCS1 format

```bash
$ ssh-keygen -m PEM -b 4096 -t rsa
$ ssh-copy-id jon@192.168.1.123
```

```php
use use Origin\Ssh\Ssh;
$ssh = new Ssh([
    'host' => '192.168.1.100',
    'username' => 'yoda',
    'password' => '',
    'privateKey' => '/home/yoda/.ssh/id_rsa',
    'publicKey' => '/home/yoda/.ssh/id_rsa.pub',
]);
```

### Executing commands

To execute a command and the output

```php
$result = $ssh->execute('pwd');
echo $ssh->getOutput()
echo $ssh->getErrorOutput();
```

### Listing files

To list the files in a directory, which returns an array of `RemoteFile` objects.

```php
$contents = $ssh->list('/etc');
Array
(
    [0] => Origin\Ssh\RemoteFile Object
        (
            [name] => magic.mime
            [directory] => /etc
            [path] => /etc/magic.mime
            [extension] => mime
            [timestamp] => 1579207151
            [size] => 111
        )
```

If you want to get the file list recursively

```php
$contents = $ssh->list('/etc',['recursive'=>true]);
```

## Sending and Receiving files

To send (upload) a file

```php
$ssh->send(__DIR__ .'/README.md', '/home/yoda/README.md');
```

To receive (download) a file

```php
$ssh->recieve('/home/yoda/README.md',__DIR__ .'/README.md');
```
