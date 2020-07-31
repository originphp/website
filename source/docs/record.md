---
title: Record
description: Record Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Record

If you code a lot there will be instances where you need to work with data that is not persisted to the database but still need to validate data from the user and then maybe send an email, save to a file or communicate with an API. Normally this would have been done a number of hacky ways, this solution works similar to `ActiveRecord`, just without persisting to database.

First create the `Record` class, in your `app/Record`

```php
namespace App\Record

use Origin\Record\Record;

class Checkout extends Record
{
    protected function initialize(): void
    {
        // create schema which is used by the FormHelper
        $this->addField('name', 'string');
        $this->addField('description', 'text');
        $this->addField('email', ['type' => 'string', 'length' => 125]);
        $this->addField('agreeToTerms', ['type' => 'boolean', 'default' => true]);
        $this->addField('age', ['type' => 'integer']);

        // setup validation rules
        $this->validate('name', 'required');
        $this->validate('email', [
            'required',
            'email'
        ]);

        $this->validate('age', ['optional', 'integer']);
    }
}
```

Working with record object from the `Controller`

```php
public function checkout()
{
    $checkout = Checkout::new();
    
    if ($this->request->is(['post'])) {
        $checkout = Checkout::patch($checkout, $this->request->data(), [
            'fields' => ['name','address','city','state','zip','cc_number','cc_expiry']
        ]);
        if ($checkout->validates()) {
            ....
        } else {
            $this->Flash->error('Please check the form the below');
        }
    }
    $this->set('checkout', $checkout);
}

```

The record object can worked with in a number of ways

```php
$checkout = Checkout::new();
$checkout->name = 'Jon Snow'; // $checkout->set('name','Jon Snow');
$name = $checkout->name; // $checkout->get('name')
unset($checkout->name); // $checkout->unset('name');
isset($checkout->name); // $checkout->has('name');
```

The `Record` object uses the `ValidateTrait`

```php
$result = $checkout->validates();
$checkout->invalidate('name'); // add validation error messages manually
```

To work with errors

```php
$errors = $checkout->errors();
$errors = $checkout->errors('email);
```

You also have some methods to check the dirtyness of the whole `Record` object or a particular field.

```php
// bool
$checkout->isDirty();
$checkout->isDirty('email');

// bool
$checkout->isClean();
$checkout->isClean('email');
```

You can also check for changes

```php
$fields = $checkout->changed();
$oldValue = $checkout->changed('email');

//bool
$checkout->wasChanged();
$checkout->wasChanged('email');
```