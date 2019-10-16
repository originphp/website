---
title: Socket
description: Socket Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Socket

## Installation

To install this package

```linux
$ composer require originphp/socket
```

## Using Sockets

```php
use Origin\Socket\Socket;
$socket = new Socket([
    'host' => 'localhost',
    'protocol' => 'tcp',
    'port' => 80,
    'timeout' => 30,
    'persistent' => false,
]);

if ($socket->connect()) {
    $socket->write("HELO mydomain.com\r\n");
    $result = $socket->read();
}

$socket->disconnect();
```

You can can also enable encryption using  `slv3`, `sslv23`, `tls`, `tlsv1`, `tlsv11` and `tlsv12` encryption methods. See the [PHP manual](https://www.php.net/manual/en/function.stream-socket-enable-crypto.php) for more information on the encryption methods.

To enable or disable encryption using `tls_client` method

```php
$socket->enableEncryption('tls'); 
$socket->disableEncryption('tls'); // tls_client
```

To enable or disable encryption using `tls_server` method

```php
$socket->enableEncryption('tls','server'); // tls_server
$socket->disableEncryption('tls','server'); // tls_server
```