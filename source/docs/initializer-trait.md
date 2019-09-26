---
title: Initializer Trait
description: Initializer Trait Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Initializer Trait

OriginPHP comes with the Initializer Trait which is enabled on both Controllers and Models, this provides the functionality to initialize a trait. Simply put the code that you want be called when the object using the trait is created in a method with the same name as the trait, but without the Trait.

```php
trait DeletableTrait
{
    public function deleteable()
    {
        // put your initialize code here
    }
}
```

You can also attach this to any where else by calling this in the constructor.

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