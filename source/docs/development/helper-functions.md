---
title: Helper Functions
description: Helper Functions Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Helper Functions

Some helper functions for common dev tasks.

## Contains

```php
$result = contains('foo','What is foo bar'); // true
```


## Left

```php
$result = left('foo','What is foo bar'); // 'What is '
```


## Right

```php
$result = right('foo','What is foo bar'); //' bar'
```

## Begins

```php
$result = begins('What','What is foo bar'); // true
```

## Ends

```php
$result = ends('bar','What is foo bar'); // true
```

## Replace

```php
$result = replace('foo','***','What is foo bar'); // 'What is *** bar'
$result = replace('foo','***','What is FOO bar',['insensitive'=>true]); // 'What is *** bar'
```

### Wrappers

```php
$result = lower($string) ; // strtolower
$result = upper($string) ; // strtoupper
$result = length($string); // strlen
```