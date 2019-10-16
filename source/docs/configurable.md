---
title: Configurable
description: Configurable Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Configurable

Components and helpers work with the `Configurable` package, to standardize the configuration process.

When you load one these classes and supply a configuration, it will be merged with the default configuration.

To get a value from the config:

```php
 $value = $this->config('foo'); // bar
```

To all of the values from the config

```php
 $array = $this->config();
```

To set a value in the config:

```php
 $this->config('foo','bar');
 $this->config(['foo'=>'bar']);
```

To set multiple values (merges config)

```php
 $this->config(['foo'=>'bar']);
```

When passing an array to the config method on the `ConfigTrait`, it will only replace values, it will not replace all the config.

If you need your component to have a default configuration, then you can set the `$defaultConfig` array property, this will be merged with any config passed when constructing the class

```php
class Foocomponent extends component
{
    protected $defaultConfig = [
        'foo' => 'bar'
    ];

    public function whatIsFoo()
    {
        return $this->config('foo');
    }
}
```