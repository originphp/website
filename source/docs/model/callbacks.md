---
title: Model Callbacks
description: Model Callbacks Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Callbacks

Models callbacks need to be registered to an an event, this provides great flexability and power when using `Concerns`.

With the exception of the `beforeFind` and `afterFind` all callbacks will pass two arguments `Entity $entity` and `ArrayObject $options`. You can cancel callbacks by returning `false` in `before` callback events.

## Initialize

This is method is really a hook so you don't have to overide the `__construct`, this is called when a `Model` is created.

```php
class User extends ApplicationModel
{
    protected function initialize(array $config) : void
    {
       
    }
}
```

## Callback Registration

To register a callback in the `initialize` method set the name of the method that you want to call on a specific event.

> Callback methods should be set as protected, if not they can be called from outside of the `Model` and will therefore violate the principle of object encapsulation.

### beforeFind

This is triggered before any find operation, the query options converted into an `ArrayObject` and then passed to the callback. Return `false` to halt the callbacks and find operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeFind('doSomething');
}

protected function doSomething(ArrayObject $options) : bool
{
    return true;
}
```

### afterFind

This is triggered after a find first or find all operation, regardless if the the find is a find first or find all, a `Origin\Model\Collection` will be returned.

```php
protected function initialize(array $config) : void
{
  $this->afterFind('modifyResults');
}

protected function modifyResults(Collection $results, ArrayObject $options) : void
{
  foreach($results as $article){
    ...
  }
}
```

### beforeValidate

This is triggered just before data is validated. Use this callback to modify data before validate. Return `false` to halt the callbacks and save operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeValidate('parseResults');
}
protected function parseResults(Entity $entity, ArrayObject $options) : bool
{
  ...
  return true;
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->beforeValidate('parseResults',[
    'on'=>['create','update']
    ]);
}
```

### afterValidate

This is triggered after the data has been validated, even if validation fails this callback is executed. You can get the validation errors from the entity by calling `errors` on the entity.

```php
protected function initialize(array $config) : void
{
  $this->afterValidate('logErrors');
}
protected function logErrors(Entity $entity, ArrayObject $options) : void
{
    if($entity->errors()){
      // do something
    }
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->afterValidate('logErrors',[
    'on'=>['create','update']
    ]);
}
```

### beforeSave

This is triggered before any save operation. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeSave('slugEntity');
}
protected function slugEntity(Entity $entity, ArrayObject $options) : bool
{
  $entity->slug = Slugger::slug($entity->title);
  return true;
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->beforeSave('slugEntity',[
    'on'=>['create','update']
    ]);
}
```

### beforeCreate

This is triggered before a record is created in the database but after `beforeSave` is triggered. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeCreate('prepareNewRecord');
}
protected function prepareNewRecord(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

### beforeUpdate

This is triggered before an existing record is updated but after `beforeSave` is triggered. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeUpdate('prepareForDb');
}
protected function prepareForDb(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

### afterCreate

This is triggered after a record is created in the database but before `afterSave` is triggered. The `options` object contains the options that were passed to the `save` method.

```php
protected function initialize(array $config) : void
{
  $this->afterCreate('logNewRecord');
}
protected function logNewRecord(Entity $entity, ArrayObject $options) : void
{
}
```

### afterUpdate

This is triggered after an existing record is updated but before `afteSave` is triggered. The `options` object contains the options that were passed to the `save` method.

```php
protected function initialize(array $config) : void
{
  $this->afterUpdate('doSometing');
}
protected function doSometing(Entity $entity, ArrayObject $options) : void
{
}
```

### afterSave

This is triggered after a save operation. The `options` object contains the options that were passed to the `save` method.

```php
protected function initialize(array $config) : void
{
  $this->afterSave('doSometing');
}
protected function doSometing(Entity $entity, ArrayObject $options) : bool
{
  if($entity->created()){
    ...
  }
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->afterSave('doSomething',[
    'on'=>['create','update']
    ]);
}
```

### beforeDelete

This is triggered just before a record is deleted. Use this callback to carry out tasks before a record is deleted. Return `false` to halt the callbacks and delete operation.

```php
protected function initialize(array $config) : void
{
  $this->beforeDelete('doSomething');
}
protected function doSomething(Entity $entity, ArrayObject $options) : bool
{
  ...
  return true;
}
```

### afterDelete

This is triggered after a record is deleted.

```php
protected function initialize(array $config) : void
{
  $this->afterDelete('doSomething');
}
protected function doSomething(Entity $entity, ArrayObject $options) : void
{
    
}
```

### afterCommit

This is triggered after data from a transaction has been committed.

```php
protected function initialize(array $config) : void
{
  $this->afterCommit('runChecks');
}
protected function runChecks(Entity $entity, ArrayObject $options) : void
{
  if($entity->deleted()){
    // do something
  }
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->afterCommit('doSomething',[
    'on'=>['create','update','delete']
    ]);
}
```

### afterRollback

This is triggered after data from a transaction has been committed.

```php
protected function initialize(array $config) : void
{
  $this->afterRollback('runBot');
}
protected function runBot(Entity $entity, ArrayObject $options) : void
{
  ...
}
```

You can also configure when these are run

```php
protected function initialize(array $config) : void
{
  $this->afterRollback('doSomething',[
    'on'=>['create','update','delete']
    ]);
}
```

## onError Callback

If your `Model` has an `onError` method it will be called if an exception is raised during a database operation.

```php
protected function onError(\Exception $exception) : void
{
}
```

## Disabling callbacks

You can also disable any callbacks that you have registered.

```php
$this->disableCallback('checkCount');
```

Then to re-enable:

```php
$this->enableCallback('checkCount');
```