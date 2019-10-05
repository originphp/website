---
title: Initializer Trait
description: Initializer Trait Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Initializer Trait

OriginPHP comes with the Initializer Trait which is enabled on both Controllers and Models, this provides the functionality to initialize a trait. 

## Configuring the Trait

To create the initialization method, add `intialize` before the name of the trait, if the trait ends with Trait, this is ignored.

```php
trait DeletableTrait
{
    protected function initializeDeleteable()
    {
        // put your initialization code here
    }
}
```

## Enable the initializer on other objects

To add this to other objects, add the `InitializerTrait` and call the `nitializeTraits` method from the constructor.

```php
use Origin\Core\InitializerTrait;

class MyClass
{
    use InitializerTrait;

    public function __construct()
    {
        $this->initializeTraits();
    }
}
```