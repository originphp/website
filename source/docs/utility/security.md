---
title: Security Utility
description: Security Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Security

The security utility provides various function related to security such as hashing, encryption, decryption.

## Hashing

### Hashing Strings (Not Passwords)

The default hashing algorithm used is `sha256`.

To hash a string (not password)

```php
use Origin\Utility\Security;
$hashed = Security::hash('foo');
```

If you want to add an additional string to the text that is being hashed, a pepper. The set pepper to true, this will use the value from `Security.pepper` from your configuration.

```php
$hashed = Security::hash('foo',['pepper'=>true]);
```

To use a different pepper

```php
$hashed = Security::hash('foo',['pepper'=>'A random string']);
```

To change the hashing type

```php
$hashed = Security::hash('foo',['type'=>'sha1']);
```

For a full list of supported algorithms

```php
$list = hash_algos()
```

### Hashing Passwords

The Security utility hashes passwords using best practices, currently this is `bcrypt` which is considered
very secure.

```php
$hashed = Security::hashPassword('secret');
```

To verify the password is correct

```php
$hashed = Security::hashPassword('secret');
$bool = Security::verifyPassword($input,$hashed); // input is user inputted password
```

## Encryption

### Generating a Secure Key

To encrypt and decrypt a string you will need a key, you can generate a random secure key.

```php
use Origin\Utility\Security;
$key = Security::generateKey(); // 33d80476167cc95c363bf7df3c95e1d1
```

The key length must be 32 bytes (256 bits) to use with the encryption decryption functions.

### Encrypting Text

To encrypt a string

```php
use Origin\Utility\Security;
$key = '33d80476167cc95c363bf7df3c95e1d1';
$encrypted = Security::encrypt('foo',$key);
```

### Decrypting Text

To decrypt an encrypted string

```php
use Origin\Utility\Security;
$key = '33d80476167cc95c363bf7df3c95e1d1';
$encrypted = 'ohRRdAydx+4wfOd7Vm+LHmmV9zBH+3r0WLQylyPMPu2RvCjX9FVgoeUBZuLYBTLM4x9NeZX7U0bUvE1bucATSQ==';
$plain = Security::decrypt($encrypted,$key);
```

## Random

To generate a cryptographically secure random string, the default length is 18.

```php
$randomString = Security::random(); // 5f31ecf661dabb04dc
```

## UID

If you need to generate a unique id, and don't need to use a UUID, then the UID method provides a more memory and disk space efficient way when working with unique ids.

> If you are generating a API token or another form of string that a user might need to type in, then use `Security::random` or `Security::uuid` instead since these use lower case characters.

To generate a cryptographically secure unique id (UID) with the default length of 15.

```php
$uid = Security::uid(); // 64cjBxfz2JPhyCQ
```

### UUID

The Security class can generate both version 4 and version 1 UUIDs.

To generate a random UUID (version 4)

```php
$uid = Security::uuid(); // 38c67382-d3ab-4430-a27e-0c719813c09f
```

For a version 1 UUID using a random MAC address and the current timestamp.

```php
$uid = Security::uuid(['timestamp'=>true]); // ac337932-e4e5-11e9-928f-8bda39fe8887
```

For a version 1 UUID using a provided MAC address and the current timestamp.

```php
$uid = Security::uuid(['macAddress'=>'00:0a:95:9d:68:16']); // 769c6fa4-e4e5-11e9-b8d5-000a959d6816
```