---
title: CSV Utility
description: CSV Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# CSV

The CSV utility makes it easy to work with CSV files.

## Create CSV from an Array

To use an array to create CSV data

```php
use Origin\Utility\Csv;

$csv = Csv::fromArray([
    ['jim','jim@example.com'],
    ['tony','tony@example.com']
]);

```

Which will give you this

```
jim,jim@example.com
tony,tony@example.com
```

You can also use keys from the array as headers

```php
use Origin\Utility\Csv;

$csv = Csv::fromArray([
        ['name'=>'jim','email'=>'jim@example.com'],
        ['name'=>'tony','email'=>'tony@example.com']
    ],['header'=>true]);

```

Which will return this

```
name,email
jim,jim@example.com
tony,tony@example.com
```

If you want to use custom headers

```php
use Origin\Utility\Csv;

$csv = Csv::fromArray([
        ['name'=>'jim','email'=>'jim@example.com'],
        ['name'=>'tony','email'=>'tony@example.com']
    ],['header'=>['First Name','Email Address']]);

```

Which will gives you this

```
"First Name","Email Address"
jim,jim@example.com
tony,tony@example.com
```

## Create an Array from CSV Data

Use the `toArray` method to create an array using CSV data.

```php
use Origin\Utility\Csv;

$csv = file_get_contents('/path/file.csv');
$data = Csv::toArray($csv);

```

If the CSV file has a headers row, then you can skip by passing an options array with the key headers set to true.

```php
$data = Csv::toArray($csv,['header'=>true]);
```

If you want to use the headers as keys for each record in the array, this will use the first row as the keys for the array.

```php
$data = Csv::toArray($csv,['header'=>true,'keys'=>true]);
```

If you want to set custom keys for each record in the array

```php
$data = Csv::toArray($csv,['keys'=>['First Name','Email Address']]);
```