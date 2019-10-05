---
title: Model Callbacks
description: Model Callbacks Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Callbacks

There are various callbacks which can be used and are triggered on certain events, from the initialize method register the callback by using the event name.

> The before callbacks do not need to return true to continue, just if they return false then the callback cycle is stopped.

## beforeFind

This is triggered before any find operation, the query options converted into an `ArrayObject` and then passed to the callback. Return `false` to halt the callbacks and find operation.

```php
public function initialize(array $config) : void
{
  $this->beforeFind('doSomething');
}

public function doSomething(ArrayObject $options) : bool
{
    return true;
}
```

## afterFind

This is triggered after a find first or find all operation, regardless if the the find is a find first or find all, a `Collection` will be returned.

```php
use Origin\Model\Collection;
public function initialize(array $config) : void
{
  $this->afterFind('modifyResults');
}

public function modifyResults(Collection $results, ArrayObject $options) : void
{
  foreach($results as $article){
    ...
  }
}
```

## beforeValidate

This is triggered just before data is validated. Use this callback to modify data before validate. Return `false` to halt the callbacks and save operation.

```php
public function initialize(array $config) : void
{
  $this->beforeValidate('parseResults');
}
public function parseResults(Entity $entity, ArrayObject $options) : bool
{
  ...
  return true;
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->beforeValidate('parseResults',[
    'on'=>['create','update']
    ]);
}
```

## afterValidate

This is triggered after the data has been validated, even if validation fails this callback is executed. You can get the validation errors from the entity by calling `errors` on the entity.

```php
public function initialize(array $config) : void
{
  $this->afterValidate('logErrors');
}
public function logErrors(Entity $entity, ArrayObject $options) : void
{
    if($entity->errors()){
      // do something
    }
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->afterValidate('logErrors',[
    'on'=>['create','update']
    ]);
}
```

## beforeSave

This is triggered before any save operation. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
public function initialize(array $config) : void
{
  $this->beforeSave('slugEntity');
}
public function slugEntity(Entity $entity, ArrayObject $options) : bool
{
  $entity->slug = Slugger::slug($entity->title);
  return true;
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->beforeSave('slugEntity',[
    'on'=>['create','update']
    ]);
}
```

## beforeCreate

This is triggered before a record is created in the database but after `beforeSave` is triggered. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
public function initialize(array $config) : void
{
  $this->beforeCreate('prepareNewRecord');
}
public function prepareNewRecord(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

## beforeUpdate

This is triggered before an existing record is updated but after `beforeSave` is triggered. The `options` object contains the options that were passed to the `save` method. Return `false` to halt the callbacks and save operation.

```php
public function initialize(array $config) : void
{
  $this->beforeUpdate('prepareForDb');
}
public function prepareForDb(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

## afterCreate

This is triggered after a record is created in the database but before `afterSave` is triggered. The `options` object contains the options that were passed to the `save` method.

```php
public function initialize(array $config) : void
{
  $this->afterCreate('logNewRecord');
}
public function logNewRecord(Entity $entity, ArrayObject $options) : void
{
}
```

## afterUpdate

This is triggered after an existing record is updated but before `afteSave` is triggered. The `options` object contains the options that were passed to the `save` method.

```php
public function initialize(array $config) : void
{
  $this->afterUpdate('doSometing');
}
public function doSometing(Entity $entity, ArrayObject $options) : void
{
}
```

## afterSave

This is triggered after a save operation. The `options` object contains the options that were passed to the `save` method.

```php
public function initialize(array $config) : void
{
  $this->afterSave('doSometing');
}
public function doSometing(Entity $entity, ArrayObject $options) : bool
{
  if($entity->created()){
    ...
  }
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->afterSave('doSomething',[
    'on'=>['create','update']
    ]);
}
```

## beforeDelete

This is triggered just before a record is deleted. Use this callback to carry out tasks before a record is deleted. Return `false` to halt the callbacks and delete operation.

```php
public function initialize(array $config) : void
{
  $this->beforeDelete('doSomething');
}
public function doSomething(Entity $entity, ArrayObject $options) : bool
{
  ...
  return true;
}
```

## afterDelete

This is triggered after a record is deleted.

```php
public function initialize(array $config) : void
{
  $this->afterDelete('doSomething');
}
public function doSomething(Entity $entity, ArrayObject $options) : void
{
    
}
```

## afterCommit

This is triggered after data from a transaction has been committed.

```php
public function initialize(array $config) : void
{
  $this->afterCommit('runChecks');
}
public function runChecks(Entity $entity, ArrayObject $options) : void
{
  if($entity->deleted()){
    // do something
  }
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->afterCommit('doSomething',[
    'on'=>['create','update','delete']
    ]);
}
```

## afterRollback

This is triggered after data from a transaction has been committed.

```php
public function initialize(array $config) : void
{
  $this->afterRollback('runBot');
}
public function runBot(Entity $entity, ArrayObject $options) : void
{
  ...
}
```

You can also configure when these are run

```php
public function initialize(array $config) : void
{
  $this->afterRollback('doSomething',[
    'on'=>['create','update','delete']
    ]);
}
```

## onError

This callback is a special callback, it is triggered if an exception is caught during the operation, this callback does not need to be registered, just create the method in your `Model`.

```php
public function onError(\Exception $exception) : void
{
}
```