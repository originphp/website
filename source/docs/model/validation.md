---
title: Model Validation
description: Model Validation Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Validation

## How to use validation rules

You can setup validation rules for your fields in the `initialize` method of your `model`.

The [validation package](https://github.com/originphp/validation) provides the validation rules used by the framework, and is installed in each project. You can also this package in your other project.

> As of version 2.6, custom validation settings `required` and `allowBlank` were renamed to `present` and `allowEmpty`, these will be renamed in the background automatically for your existing rule definitions.

The definition for validation rule is structured like this

```php
[
  'rule' => 'notEmpty',
  'message' => 'This is field cannot be empty', // error message when validation fails
  'on' => null , // null, create or update
  'present' => false, // if the key exists but does not need data
  'allowEmpty' => false, // treat null values as valid
  'stopOnFail' => false, // new in v2.6
]
```

- _rule_: this is the name of the rule to run
- _message_: error message to display if validation fails
- _on_: default is `null`. You can also set to `create` or `update` to only check validation rule when a record is created or updated.
- _present_: default is `false`. If set to true then the entity must have the field (regardless if its blank)
- _allowEmpty_: default is `false`. If set to `true` validation will pass on empty values, this is handy for some validation types and fine grain control. Lets say you create a validation rule to check MX records for an email address, there is no point running this validation rule on an empty value.
- _stopOnFail_: default is `false`. new in version 2.6. Set to true to stop processing remaining rules when validation failure occurs.

You configure your validation rules in your `Model`, here is an example how to set options for the validation rules

```php
namespace App\Model;

class Contact extends ApplicationModel
{
    protected function initialize(array $config): void
    {
        parent::initialize($config);

        $this->validate('email',[
          'email' => [ // the key for each validation rule is not important
            'rule' => 'email', 
            'message' => 'Thats not a real email address'
          ]
        ]);
    }
}
```

If you do not need to change the options for the validation rule, then you can express the validation rules like this.

```php
$this->validate('name', 'required');

$this->validate('email',[
  'required',
  'email'
]);

$this->validate('website',[
  'optional',
  'website'
]);
```

You can define a validation rule as a `string`, a `single rule array`, or array with `multiple rule arrays`

To define a rule using just the name

```php
$this->validate('password','required');
$this->validate('password',['required']);
```

To define a rule using a single array, note the `message` is not required, if it is not available the framework will use its default one.

```php
$this->validate('username', [
  'rule' => 'required',
  'message' => 'This is required'
]);
```

To define multiple validation rules on a field

```php
$this->validate('email', [
  'email' => [
    'rule' => 'email',
    'message' => 'Invalid Email address'
  ]
  'length' => [
    'rule' => ['minLength', 10]
  ]
]);
```

A `rule name` is either a `string` or an `array` depending upon if the rule takes extra arguments.

```php

$this->validate('email', [
  'rule' => 'email'
]);

$this->validate('password', [
  'rule' => ['minLength', 6]
]);

$this->validate('code', [
  'rule' => ['range', 100000,9999999]
]);

$this->validate('level', [
  'rule' =>  ['in', ['silver','gold','platinum']]
]);

```

You can also use string format like this

```php

$this->validate('email', [
  'email'
]);

$this->validate('password', [
  'minLength:6'  // [minLength, 6]
]);

$this->validate('code', [
  'range:100000:9999999', // [range, 100000,9999999]
]);

$this->validate('level', [
  'in:silver,gold,platinum' , // [in, ['silver','gold','platinum']]
]);
```

Here is an example when creating a user model

```php
class User extends ApplicationModel
{
  protected function initialize(array $config) : void
  {
    parent::initialize($config);

    $this->validate('first_name', 'required');
    $this->validate('last_name', 'required');

    $this->validate('email', [
      'required',
      'unique' => [
          'rule' => 'isUnique',
          'message' => 'Email address already in use',
          'allowEmpty' => true
        ]
    ]);

    $this->validate('password', [
      'alphaNumeric',
      'length' => [
        'rule' => ['minLength', 6]
      ]
    ]);
  }
}
```

## Validation Rules

When setting the rules the name is usually as a string, however some validation rules offer extra arguments, so then you would pass the rule name with the arguments as a single array.

```php
$this->validate('field',[
  'rule' => ['range',1,100]
]);
```

### accepted

Validate that a checkbox is checked.

```php
$this->validate('agree','accepted');
```

### after

To check that the date entered is _after_ a certain date, you can pass any `strtotime` date or string.

```php
$this->validate('start',[
  'rule' => ['after','today']
]);
```

### alpha

```php
$this->validate('username',[
  'rule' => 'alpha',
  'message' => 'Error only letters are allowed'
]);
```

### alphaNumeric

```php
$this->validate('username',[
  'rule' => 'alphaNumeric',
  'message' => 'Error only letters and numbers are allowed'
]);
```

### array

To check a value is an array

```php
$this->validate('active','array');
```

### before

To check a date is before a certain date, dates are passed to the `strtotime` function.

```php
$this->validate('start',[
  'rule' => ['before','today +3 days']
]);
```

### boolean

Checks if a value is a boolean true or false.

```php
$this->validate('active',[
  'rule' => 'boolean'
]);
```

### confirm

This is a validation rule when you need to confirm two fields are the same, usually on signup page for the password or email address. If you were to add the validation to the a field called `password` it will confirm with the `password_confirm` value.

```php
$this->validate('password',['confirm']);
```

### creditCard

To validate a credit card

```php
$this->validate('cc','creditCard');
```

If you want to validate a specific type

```php
$this->validate('cc',[
  'rule' => ['creditCard','mastercard']
]);
```

### custom (Function)

You can create your own custom validation rules, simply create a function in your `Model`

```php
// define the validation rule
public function isString($value) : bool
{
  return is_string($value);
}
```

Then call the validate function from `initialize` as you normally wood.

```php
// enable the validation rule on the field
protected function initialize(array $config) : void
{
  $this->validate('name','isString');
}
```

You can also pass more arguments

```php
protected function initialize(array $config) : void
{
  $this->validate('status',[
    'rule' => ['statusCheck','go']
  ]);
}


public function statusCheck($value, $arg1) : bool
{
  return $value1 === $arg1;
}
```

### custom (Regex)

You use custom regex patterns to validate

```php
$this->validate('code',[
  'rule' => '/^[a-zA-Z]+$/',
  'message' => 'Letters only'
]);
```

### date

Validates a date using a format compatible with the php date function. The default date format is `Y-m-d`.

```php
$this->validate('sent',[
  'rule' => 'date'
  'message' => 'Invalid date format'
]);
```

or

```php
$this->validate('sent',[
  'rule' => ['date', 'Y-m-d'],
  'message' => 'Invalid date format'
]);
```

### dateFormat

To check a specific date using a format (this is used by the date,dateTime,and time validations)

```php
$this->validate('sent',[
  'rule' => ['dateFormat', 'Y-m-d'],
  'message' => 'Invalid date format'
]);
```

### datetime

Validates a datetime using a format compatible with the php date function. The default datetime format is `Y-m-d H:i:s`.

```php
$this->validate('sent',[
  'rule' => 'datetime'
  'message' => 'Invalid datetime format'
]);
```

or

```php
$this->validate('sent',[
  'rule' => ['datetime', 'Y-m-d H:i:s'],
  'message' => 'Invalid datetime format'
]);
```

### email

Checks that a value is a valid email address.

```php
$this->validate('email',[
  'rule' => 'email'
  'message' => 'Enter a valid email address'
]);
```

You can also check that the email address has valid MX records using the getmxrr function.

```php
$this->validate('email',[
  'rule' => ['email', true],
  'message' => 'Enter a valid email address'
]);
```

### equalTo

Checks that a value equals another value

```php
$this->validate('level',[
  'rule' => ['equalTo','someString']
  'message' => 'Value must be someString'
]);
```

### extension

Checks that the extension of the filename against an list of extensions

```php
$this->validate('filename',[
  'rule' => ['extension',['csv','txt']]
  'message' => 'Only csv or text files can be uploaded'
]);
```

The extension validation rule also works with file uploads, just set the name of the field to same as what you used
on the form.

### float

Checks if a value is a float. The value must have a decimal place in it. e.g. 123.45

```php
$this->validate('amount',[
  'rule' => 'decimal',
  'message' => 'Invalid amount'
]);
```

### fqdn

To validate a Fully Qualified Domain Name (FQDN) to see that it looks thats its a valid domain, this is different than URL.

```php
$this->validate('website',[
  'rule' => 'fqdn',
  'message' => 'Invalid domain'
]);
```

You can also check the DNS records using `checkdnsrr` to ensure that is really valid and not just looks like its valid.

```php
$this->validate('website',[
  'rule' => ['fqdn',true],
  'message' => 'Invalid domain'
]);
```

### greaterThan

To check a value is greater than

```php
$this->validate('level',[
  'rule' => ['greaterThan',4],
  'message' => 'Invalid level'
]);
```

### greaterThanOrEqual

To check a value is greater than or equal

```php
$this->validate('level',[
  'rule' => ['greaterThanOrEqual',4],
  'message' => 'Invalid level'
]);
```

### hexColor

To validate a hex color

```php
$this->validate('color','hexColor');
```

### iban

To validate an IBAN number

```php
$this->validate('account','iban');
```

### in

Checks that a value is in a list.

```php
$this->validate('status',[
  'rule' => ['in',['draft','new','authorised']]
  'message' => 'Invalid status'
]);
```

The default is case sensitive search, if you want to the search to be case insensitive then you will need to pass `true` as the third option.

```php
$this->validate('status',[
  'rule' => ['in',['draft','new','authorised'],true]
  'message' => 'Invalid status'
]);
```

### integer

Checks if a value is an integer (a number without decimal places)

```php
$this->validate('quantity',[
  'rule' => 'integer',
  'message' => 'Invalid amount'
]);
```

### ip

Checks that a value is a valid ip address.

```php
$this->validate('ip_address',[
  'rule' => 'ip'
  'message' => 'Enter a valid ip address'
]);
```

To check for a `ipv4` or `ipv6` only

```php
$this->validate('ip_address',[
  'rule' => ['ip', 'ipv4']
  'message' => 'Enter a valid ip address'
]);
```

### ipRange

To check than IP address is within a certain range

```php
$this->validate('ip',[
  'rule' => ['ipRange','192.168.1.2','192.168.1.25'],
  'message' => 'Enter a valid ip address'
]);
```

### isUnique

Checks that a field value is unique in the database.

```php
$this->validate('id',[
  'rule' => 'isUnique'
  'message' => 'ID field is not unique'
]);
```

You can also check multiple values

```php
$this->validate('email',[
  'rule' => ['isUnique',['username','email']],
  'message' => 'Email and username are not unique'
]);
```

### json

To check if an input is JSON

```php
$this->validate('data','json');
```

### length

To validate a string has a certain length

```php
$this->validate('account_no',[
  'rule' => ['length', 4]
]);
```

### lessThan

To check a value is less than

```php
$this->validate('level',[
  'rule' => ['lessThan',4],
  'message' => 'Invalid level'
]);
```

### lessThanOrEqual

To check a value is less than or equal

```php
$this->validate('level',[
  'rule' => ['lessThanOrEqual',4],
  'message' => 'Invalid level'
]);
```

### lowercase

To validate a string is in all lowercase

```php
$this->validate('account','lowercase');
```

### luan

To validate a number using the luan algorithm (this is used by iban and credit card validation).

```php
$this->validate('account_number','luan');
```

### macAddress

To validate a mac address

```php
$this->validate('mac_address','macAddress');
```

### maxLength

Checks if string is less than or equals to the max length.

```php
$this->validate('username',[
  'rule' => ['maxLength',12],
  'message' => 'Username is too long'
]);
```

### md5

To validate a MD5 hash

```php
$this->validate('token','md5');
```

### mimeType

Checks if file or file upload has a certain mime type.

```php
$this->validate('filename',[
  'rule' => ['mimeType',['image/jpeg','image/png']],
  'message' => 'Invalid file'
]);
```

### minLength

Checks if string has a minimum amount of characters.

```php
$this->validate('password',[
  'rule' => ['minLength',8],
  'message' => 'Password is insecure, at least 8 characters required'
]);
```

### notBlank

Checks that a value is not empty and has anything other than whitespaces.

```php
$this->validate('name',[
  'rule' => 'notBlank',
  'message' => 'You must enter something'
]);
```

### notEmpty

> similar to notBlank except this also checks that there was no file upload.

This checks that the value is not `null`, an empty string or array or an empty upload.

```php
$this->validate('name',[
  'notEmpty'
]);
```

### notIn

To check a value is not in an array of values

```php
$this->validate('membership',[
  'rule' => ['notIn',['foo','bar']],
  'message' => 'Invalid membership'
]);
```

### numeric

Checks that a value is numeric, can be an integer or float. You can use this for validating currency amounts, as 9.99 or 10 would be valid amount.

```php
$this->validate('amount',[
  'rule' => 'numeric',
  'message' => 'Invalid amount'
]);
```

### optional

This is special validation rule, which is not part of the validation library, put this as the first validation rule
in your array when a value is optional.

If the value is empty (see `notEmpty`) then it will not run any more validation rules on this field.

```php
$this->validate('data',[
  'optional'
]);
```

### range

Checks that a value is in a range.

```php
$this->validate('number',[
  'rule' => ['range',10,20],
  'message' => 'Enter a number between 10 and 20'
]);
```

### regex

This validates an input using a provided regex, when you use a rule name with regex, it passed through
this.

```php
$this->validate('data',[
  'rule' => ['regex','/foo/i'],
  'message' => 'Enter foo'
]);
```

### required

> Similar to notEmpty but the key also must be present and if validation fails no other validation rules are run

This is special validation rule, which is not part of the validation library, put this as the first validation rule
in your array to ensure that a value is provided.

This checks that the key is present in the entity, the value is not `null`, an empty string or array or an empty upload.

If validation fails for this rule, no other validation rules for this field will be run.

```php
$this->validate('data',[
  'required'
]);
```

### time

Validates a time using a format compatible with the php date function. The default time format is `H:i:s`.

```php
$this->validate('number',[
  'rule' => 'time',
  'message' => 'Invalid time format'
]);
```

### uppercase

To validate an input is in uppercase

```php
$this->validate('title','uppercase');
```

### uuid

To validate an input is a valid UUID

```php
$this->validate('token',[
  'rule' => 'uuid',
  'message' => 'Invalid token'
]);
```

If you are going to allow uppercase

```php
$this->validate('token',[
  'rule' => ['uuid',true],
  'message' => 'Invalid token'
]);
```

### url

Checks that a value is a valid url.

By default a valid url has to have the protocol e.g. `https://www.google.com`.

```php
$this->validate('website',[
  'rule' => 'url',
  'message' => 'Invalid URL make sure you include https://'
]);
```

If you want to consider `www.google.com` a valid url (without the protocol) then you would do so like this.

```php
$this->validate('website',[
  'rule' => ['url', false]
  'message' => 'Enter without http://'
]);
```

### upload

Checks that a uploaded file has not errors

```php
$this->validate('filename',[
  'rule' => 'upload',
  'message' => 'Error uploading file'
]);
```

When uploading files, if no file is uploaded then validation will fail since an error occurred. If the file upload
is optional, then set it like this

```php
$this->validate('filename',[
  'rule' => ['upload',true],
  'message' => 'Error uploading file'
]);
```

As of version 2.6, you can use the `optional` validation rule

```php
$this->validate('filename',[
  'optional','upload'
]);
```