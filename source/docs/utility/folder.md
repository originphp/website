---
title: Folder Utility
description: Folder Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Folder

The folder utility helps you work with folders on your file system.

## Installation

> Projects created after 15th August will have these installed by default.

To install this package use Composer. 

```linux
$ composer require originphp/filesystem
```

To use the Folder utility add the following to the top of your file.

```php
use Origin\Filesystem\Folder
```

## Create

To create a folder

```php
Folder::create('/var/www/new_folder');
```

To create a folder recursively

```php
Folder::create('/var/www/level1/level2/level3/new_folder',['recursive' => true]);
```

To set the permissions on the newly created folder

```php
Folder::create('/var/www/new_folder',['mode' => 0755]);
```

## Delete

To delete a folder

```php
Folder::delete('/var/www/bye-bye')
```

To delete a folder recursively, including all files and sub directories.

```php
Folder::delete('/var/www/docs',['recursive' => true])
```

## Exists

To check if a directory exists

```php
$result = Folder::exists('/path/somedirectory');
```

## List

> As of version 2.0 of the Filesystem has been changed, path now includes the filename and directory has been added.

To list all contents of a directory

```php
$results = Folder::list('/path/somedirectory');
```

This will return an array and a `FileObject` for each file.

```php
[
    Origin\Filesystem\FileObject Object
    (
        'name' => 'foo.txt',
        'directory' => '/var/www/my_directory'
        'path' => '/var/www/my_directory/foo.txt',
        'extension' => 'txt',
        'timestamp' => 14324234,
        'size' => 1234,
        'type' => 'file'
    )
]
```

When the `FileObject` is converted to a string it will become a path e.g. `/var/www/my_directory/foo.txt`.

```php
foreach(Folder::list('/data') as $file){
    $contents = file_get_contents($file->path);
}
```

You can also get the listing recursively

```php
$results = Folder::list('/path/somedirectory', ['recursive' => true]);
```

To include directories in the results

```php
$results = Folder::list('/path/somedirectory',['directories' => true]);
```

## Copy

To copy a directory

```php
Folder::copy('/path/somedir','somedir-backup');
Folder::copy('/path/somedir','/another_path/somedir');
```

## Rename

To rename a directory

```php
Folder::rename('/path/somedir','new_name');
```

## Move

To move a directory

```php
Folder::move('/path/somedir','/another_path/somedir');
```

## Permissions

### Get Permissions

To get the permissions of a directory.

```php
$permissions = Folder::perms('/path/somedir'); // returns 0744
```

### Changing Permissions (chmod)

To change the permissions of a directory

```php
Folder::chmod('/path/somedir','www-data');
Folder::chmod('/path/somedir','www-data',['recursive' => true]); // recursive
```

### Getting the owner of a directory

```php
$owner = Folder::owner('/path/somedir'); // returns root
```

### Changing Ownership (chown)

To change the ownership of a directory

```php
Folder::chown('/path/somedir','www-data');
Folder::chown('/path/somedir','www-data',['recursive' => true]);
```

### Getting the group

To get the group that a directory belongs to.

```php
$group = Folder::group('/path/somedir'); // returns root
```

### Changing Group (chgrp)

To change the group that the folder belongs to.

```php
Folder::chgrp('/path/somedir','www-data');
Folder::chgrp('/path/somedir','www-data',['recursive' => true]);
```
