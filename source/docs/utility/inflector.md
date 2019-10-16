---
title: Inflector
description: Inflector Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Inflector

The Inflector utility changes words from singular to plural or vice versa, and it also changes cases of words as well. Under the hood, it uses a number of regex rules which can help it convert words to and from singular/plural and this is how the framework does it magic, like detecting the class name from the table name which would be in plural form.


## Converting Singular & Plural

To convert words to their singular or plural form

### Converting

```php
use Origin\Inflector\Inflector;

$singular = Inflector::singular('apples'); // apple
$plural = Inflector::plural('apple'); // apples
```

### Rules

The Inflector comes with a some standard rules which cover most words and is a good starting point for a project, however occasionally you might need to add a custom rule.

In your `config/application.php`

```php
use Origin\Inflector\Inflector;

// regex or string
Inflector::rules('singular',[
    '/(quiz)zes$/i' => '\\1'
    ]);

// regex or string
Inflector::rules('plural',[
    '/(quiz)$/i' => '\1zes'
    ]);

// string only
Inflector::rules('uncountable',['sheep']);

// string only
Inflector::rules('irregular',[
    'child' => 'children'
    ]);

```

Singular and plural rules can be both strings or regex expressions


```php
use Origin\Inflector\Inflector;

Inflector::rules('singular',['fezzes'=>'fez']);
Inflector::rules('plural',['fez'=>'fezzes']);
```

## Changing Cases

To change cases of words

```php
use Origin\Inflector\Inflector;

// change underscored words
$studyCaps = Inflector::studlyCaps('big_tree') ; // BigTree
$camelCase = Inflector::camelCase('big_tree') ; // bigTree
$human = Inflector::human('big_tree'); // Big Tree

// Change studly capped words
$underscored = Inflector::underscored('BigTree'); // big_tree

```

The Inflector also has these two methods which OriginPHP also uses to do its magic.

```php
// converts class name into into table name (plural underscored)
$tableName = Inflector::tableName('BigTree'); // big_trees

// converts table names (plural underscored) into a class name
$className = Inflector::className('big_trees'); // BigTree
```