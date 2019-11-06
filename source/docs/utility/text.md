---
title: Text Utility
description: Text Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Text Utility

The Text utility has a number of methods to help when working with strings.

## Installation

To install this package

```linux
$ composer require originphp/text
```

To use

```php
use Origin\Text\Text
```

## Converting Strings to Ascii

To convert a string into Ascii (Transliterate)

```php
$ascii = Text::toAscii('Ragnarr Loðbrók'); // Ragnarr Lodbrok
```

## Creating a Slug

To create a URL safe slug, first non ASCII characters will be converted then any non letters or numbers be will replaced with the separator.

```php
$slug = Text::slug('Who is Ragnarr Loðbrók?'); // who-is-ragnarr-lodbrok
```

## Contains

To check if a string contains a substring.

```php
$result = Text::contains('foo','What is foo bar'); // true
```

## Getting parts of strings

When you need to get part of a string before or after a substring

```php
$result = Text::left('foo','What is foo bar'); // 'What is '
$result = Text::right('foo','What is foo bar'); //' bar'
```

## Checking the start and end of a string

```php
$bool = Text::startsWith('What','What is foo bar'); // true
$bool = Text::endsWith('bar','What is foo bar'); // true
```

## Replace

To replace a substring with another string

```php
$result = Text::replace('foo','***','What is foo bar'); // 'What is *** bar'
$result = Text::replace('foo','***','What is FOO bar',['insensitive'=>true]); // 'What is *** bar'
```

## Insert

To insert values into a string using placeholders (string interpolation)

```php
$string = Text::insert('Record {id} has been updated',[
    'id'=>1234568
    ]); // Record 1234568 has been updated
```

Another example:

```php
$letter = file_get_contents('/directory/some-file');
$string = Text::insert($letter,[
    'salutation' => 'Mr.',
    'first_name' => 'Tony',
    'last_name' => 'Robbins',
    'address_1' => '100 Santa Monica Road',
    ]);
```

You can also change the place holders

```php
$string = Text::insert('Record :id has been updated',[
    'id'=>1234568,'before'=>':','after'=>''
    ]); // Record 1234568 has been updated
```


## Tokenize

For quick and easy parsing of strings, the Tokenize method makes things simple. By default `tokenize` splits strings
using a comma `,` and quotation mark `"` as an enclosure.

```php
$string = '2019-07-10 13:30:00 192.168.1.22 "GET /users/login HTTP/1.0" 200 1024';
$result = Text::tokenize($string,['separator'=>' ']);

/*
// Will give you this
[
    '2019-07-10',
    '13:30:00',
    '192.168.1.22',
    'GET /users/login HTTP/1.0',
    '200',
    '1024'
];
*/
```

You can also supply keys instead which will be mapped.

```php
$string = '2019-07-10 13:30:00 192.168.1.22 "GET /users/login HTTP/1.0" 200 1024';
$result = Text::tokenize($string,[
    'separator'=>' ',
    'keys'=>['date','time','ip','request','code','bytes']
]);

/*
// Will give you this
[
    'date'=>'2019-07-10',
    'time'=>'13:30:00',
    'ip' => '192.168.1.22',
    'request' =>'GET /users/login HTTP/1.0',
    'code'=>'200',
    'bytes'=>'1024'
];
*/
```

## Truncate

To truncate a string if it is longer than a specific length. The default length is 30.

```php
$truncated = Text::truncate($string,['length'=>50,'end'=>'... truncated']);
```

## Word Wrap

To wordwrap a string

```php
$wrapped = Text::wordWrap($string); // default is 80
$wrapped = Text::wordWrap($string,['width'=>50]);
```

## Other

Other handy string functions  (through `mb_string`)

```php
$lowerCase = Text::lower($string);
$uppserCase = Text::upper($string);
$int = Text::length($string);
```