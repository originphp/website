---
title: Concerns - Model
description:  Concerns - Model Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Concerns

A `Concern` is code which you share between `Models` that go in the `app/Model/Concern` folder. Do not use `Concerns` to reduce code in your `Models` that is an anti-pattern, only use a `Concern` when you need to share code across multiple `Models` or create a behavior that can be dropped into a `Model`.

The first thing to do, is generate the classes

```linux
$ bin/console generate concern_model Sluggable
```

This will create two files

```
[ OK ] /var/www/app/Model/Concern/Sluggable.php
[ OK ] /var/www/tests/TestCase/Model/Concern/SluggableTest.php
```

For more information on code generation see the [code generation guide](/docs/development/code-generation).

## How to Use

Since `Concerns` are `traits`, you can add to any `Model` like this

```php
use App\Model\Concern\Sluggable;
class User extends ApplicationModel
{
  use Sluggable;
}
```

## Callbacks

You can use callbacks with `Concerns`, the initialization method are `Concern` starts with Initialize then has the trait name (without the Trait suffix), for example `initializeTagabble`.

### Initialization

When the `Model` is created, the initialization method for the `Concern` is called, if it exists, from here you can setup the `Concern`.

```php
namespace App\Model\Concern;

trait MyConcern
{
    protected function initializeMyConcern(): void
    {
    }
}
```

### Registering Callback from the Concern

To be able to use `Model` callbacks with your `Concern` you must register them, using the `Model` callback registration method such as `beforeFind`, `afterFind` etc.

```php
protected function initializeMyConcern(): void
{
  $this->afterFind('modifyResults');
}

protected function modifyResults(Collection $results, ArrayObject $options): void
{
  foreach($results as $article){
    ...
  }
}
```

For more information see the [Model Callbacks Guide](/docs/model/callbacks) 

### Available Callback Events

The available callback events in the order that they are called:

** Finding ** 
- beforeFind
- afterFind

** Saving **

- beforeValidate
- afterValidate
- beforeSave
- beforeCreate/beforeUpdate
- afterCreate/afterUpdate
- afterSave
- afterRollback/afterCommit

** Deleting ** 
- beforeDelete
- afterDelete
- afterRollback/afterCommit

For more information see the [Model Callbacks Guide](/docs/model/callbacks) 

### Disabling callbacks

To disable a callback use the `Model` method:

```php
$this->disableCallback('processResults');
```

Then to re-enable:

```php
$this->enableCallback('processResults');
```

## Model Concern

Here is an example `Model` `Concern`

```php
namespace App\Model\Concern;

use Origin\Model\Entity;
use Origin\Text\Text;
use ArrayObject;

trait Sluggable
{
    /**
     * Initialization method, here you can register callbacks or configure model
     * associations
     *
     * @return void
     */
    protected function initializeSluggable(): void
    {
        $this->beforeSave('slugTitle');
    }

    /**
     * Is a registered callback
     *
     * @param \Origin\Model\Entity $record
     * @param ArrayObject $options
     * @return boolean|void
     */
    protected function slugTitle(Entity $record, ArrayObject $options)
    {
        if ($record->title) {
            $record->slug = Text::slug($record->title);
        }
    }
}
```

Then add the `Concern` to a `Model`.

```php
use App\Model\Concern\Sluggable;
class Article extends ApplicationModel
{
  use Sluggable;
}
```

## Model Concerns

OriginPHP comes with a few `Concerns` 

### Timestampable

This `Concern` updates created and updated fields when creating or saving records, this is also enabled by default in the `ApplicationModel`.

### Delocalizable

This `Concern` delocalizes data. This is enabled by default in the `ApplicationModel`, and during the save process the data is parsed into MySQL format.

### Elasticsearch

Automatically integrate your application with Elasticsearch. See the [Elasticsearch Plugin](/docs/plugins/elasticsearch) for more information.

### Cacheable

Automatically cache the database results in your application.

```php
use Origin\Model\Concern\Cacheable;

class ApplicationModel extends Model
{
  use Cacheable;
}
```

By default it will use your `default` cache configuration, if you want to change this

```php
protected function initialize(array $config): void
{
    parent::initialize($config);
    $this->cacheConfig('redis');
}
```

#### Invalidating Cache Manually

If you use model methods which modify the database but do not trigger callbacks such as `updateColumn`,`updateAll`,`deleteAll`,`increment` or `decrement` then you should manually call `invalidateCache`.

```php
public function doSomething() 
{
  $this->updateColumn(1000,'status','new');
  $this->invalidateCache();
}
```

#### Disabling and Reenabling

Sometimes you might want to disable the Caching features for a particular operation.

```php
public function doSomething() 
{
  $this->disableCache();
  ... // your code here
  $this->enableCache(); // put it back
}
```

#### Cache Configuration

Configure your cache in `config/cache.php`

```php
Cache::config('apcu', [
    'engine' => 'Apcu',
    'duration' => '+15 minutes', // string or number of seconds e.g. 3600,
]);
```