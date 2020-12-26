---
title: Record
description: Record Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Record

There will be instances where you need to work with data that is not persisted to the database but still need to validate data from the user and then maybe send an email, save to a file or communicate with an API. Normally this would have been done a number of hacky ways, this solution works similar to `ActiveRecord`, just without persisting to database.

Use the generate command to create the `Record` and its test for you, in your `Model` folder. 

```linux
$ bin/console generate record StripeCustomer
[ OK ] /var/www/app/Model/StripeCustomer.php
[ OK ] /var/www/tests/TestCase/Model/StripeCustomerTest.php
```

If you are using for this only Form validation for example a Contact page, then you can use the `Form` generator instead, this will place the files in the `Form` folder to keep things separate.

```linux
$ bin/console generate form Checkout
[ OK ] /var/www/app/Form/CheckoutForm.php
[ OK ] /var/www/tests/TestCase/Form/CheckoutFormTest.php
```

Then you can adjust like this, defining the schema for the field such as type `string`, `text`, `integer`, `decimal`, `date`,`time`,`datetime` or `boolean`. Then add the validation rules, and if needed register any callbacks.

```php
namespace App\Form

use Origin\Model\Record;

class CheckoutForm extends Record
{
    protected function initialize(): void
    {
        // create schema (if needed) which is used by the FormHelper
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
    $checkout = CheckoutForm::new();
    
    if ($this->request->is(['post'])) {
        $checkout = CheckoutForm::patch($checkout, $this->request->data(), [
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
$checkout->name = 'Jon Snow'; // $checkout->set('name','Jon Snow');
$name = $checkout->name; // $checkout->get('name')
unset($checkout->name); // $checkout->unset('name');
isset($checkout->name); // $checkout->has('name');
```

The `Record` object provides two static methods, `new` and `patch` both return a new instance, with patch designed to take an array of data an patch an existing `Record`

```php
$checkout = CheckoutForm::new(); // CheckoutForm::new(['name' => 'Jon Snow']);
$checkout = CheckoutForm::new(['name' => 'Jon Snow']);

$checkout = CheckoutForm::patch($checkout, $_POST);
```

Both those methods accept `fields` option, to prevent mass assignment, by restricting which fields
can be added.

To check if the `Record` is valid or to invalidate a particular field

```php
$result = $checkout->validates();
$checkout->error('name','Invalid name'); // add validation error messages manually
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

## Callbacks

You can register `beforeValidate` and `afterValidate` callbacks

```php
protected function initialize(): void
{
    $this->beforeValidate('changeName');
}

protected function changeName(): void
{
    $this->name = strtoupper($this->name);
}
```


## Accessors & Mutators

The record class works similar to entities, so you can also use accessors and mutators.

### Accessor

To create an accessor

```php
namespace App\Model\Record;
use Origin\Model\Record;

class ContactForm extends Record
{
  protected function getFullName()
  {
    return $this->first_name . ' ' . $this->last_name;
  }
}
```

So you can do this and it will be call the method

```php
echo $contact->full_name;
```

#### Virtual Fields

You can also set `accessors` as virtual fields so that when you export data to JSON or XML or an array this value is included. Add the fields to the `virtual` property.

```php
class ContactForm extends Record
{
  protected $virtual = ['full_name'];
}
```

### Mutator

To create a mutator

```php
namespace App\Model\Record;
use Origin\Model\Record;

class ContactForm extends Record
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
class ContactForm extends Record
{
  protected $hidden = ['password','tenant_id'];
}
```