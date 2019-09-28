---
title: Model Callbacks
description: Model Callbacks Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Callbacks

Create any of the following methods in your `Model` or `Behavior` and these will be triggered automatically.

## beforeFind

This is called before any find operation, the query options converted into an `ArrayObject` and then passed to the 
callback. Return `false` to halt the callbacks.

```php
public function beforeFind(ArrayObject $options) : bool
{
    return true;
}
```

## afterFind

This is called after a find first or find all operation.

```php
use Origin\Model\Collection;
public function afterFind(Collection $results, ArrayObject $options) : void
{
  foreach($results as $article){
    $this->doSomething($article);
  }
}
```

## beforeValidate

This is called just before data is validated and must return `true`. Use this callback to modify data before validate

```php
public function beforeValidate(Entity $entity, ArrayObject $options) : bool
{
  $this->doSomething($entity);
  return true;
}
```

## afterValidate

This is called after the data has been validated, even if validation fails this callback is executed. You can get the validation errors from the entity by calling `errors` on the entity.

```php
public function afterValidate(Entity $entity, ArrayObject $options) : bool
{
    if($entity->errors()){
      // do something
    }
}
```


## beforeSave

This is called before any save operation. The `options` object contains the options that were passed to the `save` method. The filter must return `true` or saving will stopped.

```php
public function beforeSave(Entity $entity, ArrayObject $options) : bool
{
  $entity->slug = Slugger::slug($entity->title);
  return true;
}
```

## beforeCreate

This is called before a record is created in the database but after `beforeSave` is called. The `options` object contains the options that were passed to the `save` method. The filter must return `true` or saving will stopped.

```php
public function beforeCreate(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

## beforeUpdate

This is called before an existing record is updated but after `beforeSave` is called. The `options` object contains the options that were passed to the `save` method. The filter must return `true` or saving will stopped.

```php
public function beforeUpdate(Entity $entity, ArrayObject $options) : bool
{
    return true;
}
```

## afterCreate

This is called after a record is created in the database but before `afterSave` is called. The `options` object contains the options that were passed to the `save` method.

```php
public function afterCreate(Entity $entity, ArrayObject $options) : void
{
}
```

## afterUpdate

This is called after an existing record is updated but before `afteSave` is called. The `options` object contains the options that were passed to the `save` method.

```php
public function afterUpdate(Entity $entity, ArrayObject $options) : void
{
}
```

## afterSave

This is called after a save operation. The `options` object contains the options that were passed to the `save` method.

```php
public function afterSave(Entity $entity, ArrayObject $options) : bool
{
  if($entity->created()){
    $this->doSomething($entity->id);
  }
}
```

## beforeDelete

This is called just before a record is deleted must return `true`. Use this callback to carry out tasks before a record is deleted.

Note: When saving (including creating) or deleting a record the primary key can be found on the id property of the model.

```php
public function beforeDelete(Entity $entity, ArrayObject $options) : bool
{
  $this->doSomething($entity->id);
  return true;
}
```

## afterDelete

This is called after a record is deleted.

```php
public function afterDelete(Entity $entity, ArrayObject $options) : void
{
    
}
```

## onError

This is called if an exception is caught during the operation.

```php
public function onError(\Exception $exception) : void
{
}
```

## afterCommit

This is called after data from a transaction has been committed.

```php
public function afterCommit(Entity $entity, ArrayObject $options) : void
{
  if($entity->deleted()){
    // do something
  }
}
```

## afterRollback

This is called after data from a transaction has been committed.

```php
public function afterRollback(Entity $entity, ArrayObject $options) : void
{
  // do something
}
```