---
title: Model Behaviors
description: Model Behaviors Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Behaviors

A `Behavior` is used to extend functionality in a model, like a plugin for this. Let's say you want to create a taggable, soft delete, ACL or Tree behavior.

Behaviors have Behavior added on the end of the class and filename.

To create a behavior you will need to create a file in `app/Model/Behavior` folder and call it `FooBehavior`.

```php

namespace App\Model\Behavior;
use Origin\Model\Behavior\Behavior;

class FooBehavior extends Behavior
{
  // put your default configuration here. this will be merged
  protected $defaultConfig = [];

  public function doSomething(){
    return true;
  }
}

```

To load the behavior, call `loadBehavior` from the `initialize` method.

```php
    class Article extends ApplicationModel
    {
      public function initialize(array $config)
      {
          parent::initialize($config);
          $this->loadBehavior('Foo');
      }
    }

```

To use a behavior the functions will be added to the model.

```php
class Article extends ApplicationModel
{
    public function demo()
    {
        if($this->doSomething()){
        return true;
        }
        return false;
    }
}
```

To access a model from within a behavior

```php
class WidgetBehavior extends Behavior
{
    public function doSomething(){
        $widgets = $this->model()->find('all');
    }
}
```

Behaviors have the same [callbacks](/docs/model/callbacks) functions as models. So just add the callbacks that you need.

Sometimes you will need to disable or unload behaviors, to do this you will need access the behavior registry

```php
    class Article extends ApplicationModel
    {
      public function import()
      {
          $this->disableBehavior('timestamp');

          ...

          $this->enableBehavior('timestamp');

      }
    }
```

## Behavior Configuration

Behaviors work with the `ConfigTrait`, standardizing and simplifying how you work with configuration. See the [ConfigTrait guide](/docs/config-trait) for more information.

## Framework Behaviors

### Timestamp

This behavior updates created and updated fields when creating or saving records.

### Delocalize Behavior

This behavior delocalizes data such as dates and numbers from locale format, and converts this into MySQL format for saving.

### Elasticsearch Behavior

Automatically integrate your application with Elasticsearch. See the [Elasticsearch Behavior](/docs/model/elasticsearch-behavior) for more information.