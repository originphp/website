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

The key length must be a minimum length of 32 bytes (256 bits).

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

It will return `null` if it is unable to decrypt because the key or data is wrong. It will return `false`, if it might have been tampered with.