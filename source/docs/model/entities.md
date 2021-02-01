---
title: Model Entities
description: Model Entities Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Entities

An entity is a single row from the database. Using find first or find all will return either a entity object or a collection of entity objects. 

Note: The collection object from a find all is not the same as the collection utility, it is a lighter version of this, you can still pass the results from find all to a collection object.

```php
  $article = $this->Article->find('first');

  echo $article->title;

  # hasOne & BelongsTo are singular are singular camelCase
  $author = $article->author;

  echo $author->name;

  # hasMany and hasAndBelongsToMany will be plural camelCase
  foreach($article->comments as $comment){
    echo $comment->text;
  }

```

From within the controller you will want to create entity from request data, you do this by accessing the model methods `new` and `patch`. Arrays of data passed through these methods go through a marshaling process.

```php 
  $user = $this->User->new($this->request->data());
```

To get a blank entity for a model do this:

```php
  $user = $this->User->new();
  $user->name = 'james'
  $user->email = 'james@example.com'
```

If you want to create multiple entities from form data it should be like this:

```php 
  $formData = [
    ['name' => 'James'],
    ['name' => 'John']
  ];
  $entities = $this->User->newEntities($formData);
```

This will return a collection object with two entities.

If you are editing an existing record, then use `patch`. Only fields that have been modified will be
saved. The field will be classed as modified even if the value stays the same, since we are going patch the existing  entity with the data, in this case from the request.

```php 
  $user = $this->User->patch($existingEntity,$this->request->data());
```

## Isset

```php
$result = isset($article->title);
$result = $article->has('title')
```

## Set

```php
$article->title = $title;
$article->set('title',$title)
```

You can also set many properties at once

```php
$article->set([
  'title' => 'Article Title',
  'status' => 'draft'
  ]);
```

## Get

```php
$title = $article->title;
$title = $article->get('title');
```

## Has

To check if the entity has a property

```php
$article->has('title');
```

## Errors

Validation errors are contained within the entities.

To get all errors

```php
$errors = $entity->errors();
```

To get error(s) for a field

```php
$errors = $entity->errors('first_name');
```

To set errors manually

```php
$entity->error('email','invalid email address');
```

## Other Methods

### reset

This resets the modified property and any validation errors.

```php
$entity->reset();
```

### dirty

> You can also use modified

Gets a list of fields that are dirty or check if a field was modified (does not mean it was changed)

```php
$fields = $entity->dirty();
$bool = $entity->dirty('email');
```

### Created

After saving an entity you can see if it was a newly created record in the database

```php
$bool = $entity->created();
```

### Deleted

You can check if the entity was deleted from the database

```php
$bool = $entity->deleted();
```

### name

Gets the `Model` name for the `Entity`

```php
$modelName = $entity->name();
```

### properties

Get the `entity` properties

```php
$list = $entity->properties();
```

## isEmpty

Check if the value of a property is either an empty string, empty array or null value

```php
$entity->isEmpty('name');
```

## notEmpty

NotEmpty uses the `isEmpty` method, and checks that the value is not a empty string '' or empty array or null value.

```php
$entity->notEmpty('name');
```

### toArray

Converts the entity into an array.

```php
$array = $entity->toArray();
```

### toJson

Converts the entity into a JSON string.

```php
$json = $entity->toJson();
```

## Custom Entity Classes

By default the `Origin\Model\Entity` class is used for each row of a model, however you can use your own `Entity` classes.

Create a your `Entity` files in the `app/Model/Entity` folder

```php
namespace App\Model\Entity;
use Origin\Model\Entity;

class User extends Entity
{
  public function softDelete()
  {
    $this->deleted = now();
  }
}
```

## Accessors & Mutators

### Accessor

To create an accessor

```php
namespace App\Model\Entity;
use Origin\Model\Entity;

class User extends Entity
{
  protected function getFullName()
  {
    return $this->first_name . ' ' . $this->last_name;
  }
}
```

Now when you work with results from the database

```php
echo $user->full_name;
```

#### Virtual Fields

You can also set `accessors` as virtual fields so that when you export data to JSON or XML or an array this value is included. Add the fields to the `virtual` property.

```php
class User extends Entity
{
  protected $virtual = ['full_name'];
}
```

### Mutator

To create a mutator

```php
namespace App\Model\Entity;
use Origin\Model\Entity;

class User extends Entity
{
  protected function setFirstName($value)
  {
    return ucfirst(strtolower($value));
  }
}
```

## Hiding Fields

To hide fields when being exported to an array, JSON or XML set the `hidden` property.

```php
class User extends Entity
{
  protected $hidden = ['password','tenant_id'];
}
```