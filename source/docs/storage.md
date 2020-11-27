---
title: Storage Guide
description: Storage Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Storage

The Storage utility provides an easy way to access different types of storages from local disk, FTP, SFTP, ZIP and Amazon S3. Its a unified approach for working with different storages.

The default configuration for Storage is the local storage engine,which stores data on the disc in the storage folder. You can configure different types of storages in the `config/storage.php` file.

## Using Storage

The Storage utility always uses the Local storage engine as default unless you tell it otherwise.

### Writing To Storage

```php
use Origin\Storage\Storage;
Storage::write('test.txt','hello world!');
```

You can also write to folders directly. Folders in the tree that do not exist will be created automatically.

```php
Storage::write('my_folder/test.txt','hello world!');
```

### Reading From Storage

```php
use Origin\Storage\Storage;
$contents = Storage::read('my_folder/test.txt');
```

### Checking

To find out if a file or folder exists

```php
$result = Storage::exists('test.txt');
$result = Storage::exists('my_folder/test.txt');
```

### Deleting From Storage

To delete files or folders

```php
Storage::delete('my_folder/test.txt');
Storage::delete('my_folder');
```

Folders are deleted recursively automatically, when using delete.

### Listing Storage Contents

> Version 2.0 released 26.09.20 returns different results for list, uses path instead of name. 

To list the files on the storage

```php
use Origin\Storage\Storage;
$allFiles = Storage::list();
```

Storage contents are listed recursively and it will provide you with an array of `FileObjects`. Each file has is an object which can be accessed as an array or an object

```php
// Will look like this
Origin\Storage\FileObject Object
(
    [name] => july.csv
    [directory] => transactions
    [path] => transactions/july.csv
    [extension] => txt
    [timestamp] => 1601121922
    [size] => 32
)

echo $file->name;
echo $file['name'];
```

When the `FileObject` is converted to a string it will become a path e.g. `transactions/july.csv`

```php
foreach(Storage::list('CSV') as $file){
    $contents = Storage::read($file->path);
}
```

If you just want the files of particular folder, then it will list all files recursively under that folder.

```php
use Origin\Storage\Storage;
$files = Storage::list('my_folder');
```

### Working with Multiple Storages


Whether you are using multiple storage engines, or you multiple configurations for a single storage engine, the Storage utility is flexible.

You can get the configured Storage volume

```php
$volume = Storage::volume('sftp-backup');
$data = $volume->read('transactions.csv');
```

Or you can pass an options array telling the Storage object which configuration to use

```php
$data = Storage::read('transactions.csv',[
    'config' => 'sftp-backup'
]);
```

## Storage Engines

Configuration for storage engines can be found in `config/storage.php`, it best for localized settings to be actually stored
in `config/.env` then use the `env` function to get, for example.

in `config/.env` add

```
USERNAME=user@example.com
```

Then in the config file

```php
return [
    'username' => env('username')
]
```

### Local

The local storage simply works with data from the drive.

```php
// config/storage.php
use Origin\Storage\Engine\LocalEngine;

return [
    'default' => [
        'root' => '/var/www/storage',
        'className' => LocalEngine::class
    ]
];
```

### FTP

Then you need to configure this

```php
// config/storage.php
use Origin\Storage\Engine\FtpEngine;

return [
    'default' => [
        'host' => 'example.com',
        'port' => 21,
        'username' => 'james',
        'password' => 'secret',
        'ssl' => false,
        'className' => FtpEngine::class
    ]
];
```

options for configuring FTP include:

- host: the hostname or ip address
- port: the port number. default 21
- username: the ftp username
- password: the ftp password
- timeout: default 10 seconds
- passive: deafult false
- root: the root folder of the storage within your ftp account
- ssl: default: false

### SFTP

To use the SFTP storage you need to install the `phpseclib` library.

```linux
$ composer require phpseclib/phpseclib
```

Then you need to configure the Storage.

```php
// config/storage.php
use Origin\Storage\Engine\SftpEngine;

return [
    'default' => [
        'host' => 'example.com',
        'port' => 22,
        'username' => 'james',
        'password' => 'secret'
        'className' => SftpEngine::class
    ]
];
```

If you use want to use a private key to login, you can either provide the filename with the full path or the contents of the private key itself.


```php
// config/storage.php
use Origin\Storage\Engine\SftpEngine;

return [
    'default' => [
        'host' => 'example.com',
        'port' => 22,
        'username' => 'james',
        'privateKey' => '/var/www/config/id_rsa'
        'className' => SftpEngine::class
    ]
];
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


### S3

The S3 Engine works with [Amazon S3](https://aws.amazon.com/s3/) and any other object storage server which uses the S3 protocol, for example [minio](https://min.io/).

To use the S3 Engine, you need to install the Amazon AWS SDK

```linux
$ composer require aws/aws-sdk-php
```

Then you can configure the S3 engine like this

```php
// config/storage.php
use Origin\Storage\Engine\S3Engine;

return [
    'default' => [
    'className' => S3Engine::class
    'credentials' => [
        'key' => env('S3_KEY'), // * required
        'secret' => env('S3_SECRET'), // * required
    ],
    'region' => 'us-east-1', // * required
    'version' => 'latest',
    'endpoint' => env('S3_ENDPOINT'), // for S3 comptabile protocols
    'bucket' => env('S3_BUCKET') // * required
    'className' => S3Engine::class
    ]
];
```

Options for configuring the `S3` engine are:

- credentials: this is required and is an array with both `key` and `secret`
- region: The label for location of the server
- version: version setting
- endpoint: If you are not using Amazon S3. e.g. `http://127.0.0.1:9000`
- bucket: The name of the bucket, this is required and the bucket should exist.

#### Minio Server (S3)

To fire up your own minio server locally you can run the docker command

```linux
$ docker run -p 9000:9000 minio/minio server /data
```

You can access this also using your web browser at `http://127.0.0.1:9000`.

## Zip

To use the ZIP storage engine, provide the filename with a full path, if you just want to work with ZIP
files but not as a constant storage volume then see [origin/zip](/docs/utility/zip).

```php
// config/storage.php
use Origin\Storage\Engine\S3Engine;
return [
    'default' => [
        'file' => '/var/www/backup.zip',
        'className' => ZipEngine::class
    ]
];
```