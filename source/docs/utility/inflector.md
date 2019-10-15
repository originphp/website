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
use Origin\Utility\Inflector;

$singular = Inflector::singular('apples'); // apple
$plural = Inflector::plural('apple'); // apples
```

### Dictionary

The Inflector comes with a some standard rules which cover most words and is a good starting point for a project. The user defined dictionary is a easy to use way to quickly define certain words that might not be picked up by the Inflector, but still enable the framework to continue to do its magic. 

In your `config/application.php` add any custom words in lowercase

```php
use Origin\Utility\Inflector;

Inflector::add('cactus', 'cacti');
```

When you use the `add` method the framework can now convert catctus to cacti back and forth in both underscored lower case or studly caps (e.g. HappyPeople).

### Rules

Regex rules gives you the most flexibility, and will work beyond tables and class names (in other words you can inflect words with different capitalizations).

In your `config/application.php` add any new rules like this

```php
use Origin\Utility\Inflector;

Inflector::rules('singular',[
    '/(quiz)zes$/i' => '\\1'
    ]);

Inflector::rules('plural',[
    '/(quiz)$/i' => '\1zes'
    ]);
```

## Changing Cases

To change cases of words

```php
use Origin\Utility\Inflector;

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

