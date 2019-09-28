---
title: Initializer Trait
description: Initializer Trait Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Initializer Trait

OriginPHP comes with the Initializer Trait which is enabled on both Controllers and Models, this provides the functionality to initialize a trait. 

## Configuring the Trait

Create a trait with the Trait suffix, and put the code that you want be called when the object using the trait is created in a method with the same name as the trait, but without the trait suffix.

```php
trait DeletableTrait
{
    public function deleteable()
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