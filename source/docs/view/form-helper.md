---
title: Form Helper
description: Form Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Form Helper

The Form helper does the heavy lifting when working with forms in your app.

## Creating a Form

To create a form you use the create method and once you have finished adding the elements you call the end method which simply closes the form the tag. You should the pass an entity (it can be blank) for the model that you are working with, if you don't the form helper will work with the data from the request.

```php
    echo $this->Form->create($article);
    ...
    echo $this->Form->end();

```

If you want to create a form without this you can, however the database wont be introspected and fields types cant be detected, when creating a form this way, the request data is used. Validation errors are stored in entities, so using a form without this method cannot be used to display validation errors automatically.

```php
$this->Form->create();
$this->Form->create(null,$options);
```

The form options are as follows:

- type: default is `post`, you can also set this to `get` or `file` if you are going to upload a file.
- url: default is the current url, however you can use any url you want.

If you pass any other keys in the options, they will be used as attributes for the form tag, for e.g class.

## Form Controls

The form control is the main feature on the form helper, it will create a form element based depending upon how your database is setup. Form controls create a label and wrap the input in a div as well as display validation errors etc.

E.g.

```php
echo $this->Form->control('first_name');
```

Will output:

```html
<div class="form-group text">
    <label for="name">First Name</label>
    <input type="text" name="name" class="form-control" id="name">
</div>
```

## Form Control options

The options for control allow you to change the default settings.

- type: use this to override the type, text,textarea,number,date,time,datetime,select,checkbox,radio,password and file
- div: change the name of the div that input element is wrapped in e.g. form-group
- class: change the name of the class of the input element e.g. form-input
- label: (default:true), boolean, a string which will be the label text, or an array with the text key and any other options
 e.g. class

 All other options will be passed to the form element, if it does not recognise the option it will treat it as an attribute.

```php
echo $this->Form->control('name',['placeholder'=>'enter your name']);
```

The standard options which be used in most form inputs which are used by the control method

- id: (default:true) bool or set a string with the name that you want
- name: change the name of the options
- value: set the default value to be used by the input

Any other keys passed to the form inputs will be treated as attributes for html tag e.g. class, pattern,placeholder etc.

## Input Types

### Text

This will be display a text box.

```php
echo $this->Form->text('first_name');
```

```html
<input type="text" name="first_name">
```

### TextArea

This displays a textarea element

```php
echo $this->Form->textarea('some_name');
```

```html
<textarea name="some_name"></textarea>
```

### Select

The select element works slightly different, since the second argument is for generating the options in the select, and the
third argument is where the options/attributes are passed.

```php
echo $this->Form->select('categories', [1=>'First',2=>'Second']);
```

```html
<select name="categories">
    <option value="1">First</option><option value="2">Second</option>
</select>
```

When working with selects you might want to allow an empty option.

```php
$selectOptions = [1=>'First',2=>'Second'];
echo $this->Form->select('categories',$selectOptions , ['empty'=>'select one']);
```

Which will output this:

```html
<select name="categories">
    <option value="">select one</option>
    <option value="1">First</option>
    <option value="2">Second</option>
</select>
```

### Checkbox

To generate a checkbox

```php
echo $this->Form->checkbox('subscribe');
```

This will output this

```html
<input type="hidden" name="subscribe" value="0"><input type="checkbox" name="subscribe" value="1">
```

If you want it checked by default.

```php
echo $this->Form->checkbox('subscribe',['checked'=>true]);
```

### Radio

To generate a radio input

```php
echo $this->Form->radio('plan', [1000=>'Basic',1001=>'Premium']);
```

```html
<label for="plan-1000"><input type="radio" name="plan" value="1000" id="plan-1000">Basic</label>
<label for="plan-1001"><input type="radio" name="plan" value="1001" id="plan-1001">Premium</label>
```

To check a value by default, set the value in options.

```php
echo $this->Form->radio('plan', [1000=>'Basic',1001=>'Premium'], ['value'=>1001]);
```

### File

To create a file upload you need to set the type option when creating the form and then call file method.

```php
echo $this->Form->create(null, ['type'=>'file']);
echo $this->Form->file('contacts');
echo $this->Form->button('Import Contacts');
echo $this->Form->end();
```

This would create the following html:

```html
<form enctype="multipart/form-data" method="post" action="/contacts/import">
<input type="file" name="contacts">
<button type="submit">Import Contacts</button>
</form>
```

Then you can access the file data from the `request` object when the form has been submitted.

```php

print_r($this->request->data('contacts'));

// The array would look like this
Array
(
    [name] => bitcoin.pdf
    [type] => application/pdf
    [tmp_name] => /tmp/phpgCQmO0
    [error] => 0
    [size] => 184292
)
```

### Password

This generates a password field.

```php
echo $this->Form->password('secret');
```

```html
<input type="password" name="secret">
```

### Date, Datetime Time and Number

These are just text elements and are place to give flexibility, the control method will generate html through these based the field type in the database. You can hook these as you see fit, or change the default templates for them.

## PostLinks

To create a link which when clicked on sends a post request. This is used in the framework during code generation for delete links, this allows you to ask the user to confirm and make sure people don't call the url manually.

```php
echo $this->Form->postLink('delete','/contacts/delete/1234',['confirm'=>'Are you sure you want to delete this?']);
```

This will output this:

```html
<form name="link_1000" style="display:none" method="post" action="/contacts/delete/1234">
<input type="hidden" name="_method" value="POST"></form>
<a href="#" method="post" onclick="if (confirm(&quot;are you sure?&quot;)) { document.link_1000.submit(); } event.returnValue = false; return false;">delete</a>
```

## Buttons

Buttons created via the button method in the form helper are automatically treated as submit buttons, if you don't want this then pass the `type` option as `button`.

```php
echo $this->Form->button('save');
echo $this->Form->button('cancel',['type'=>'button','onclick'=>'back();']);
```

## Controls for Associated Data

To create a form for associated data you use `model.fieldName` for `hasOne` and `belongsTo`, or `models.0.fieldName` for `hasMany` and `hasAndBelongsToMany`. Remember model names should be in lowercase.

```php
// Create for the Article Model
echo $this->Form->create($article);

// $article->title
echo $this->Form->control('title');

// $article->author->name - BelongsTo and HasOne
echo $this->Form->control('author.name');

// HasMany and HasAndBelongsToMany
echo $this->Form->control('tags.0.name');
echo $this->Form->control('comments.0.text');
echo $this->Form->control('comments.1.text');
```

When the form is posted the request data will look like this:

```php
Array
(
    [title] => How to save associated data
    [author] => Array
        (
            [name] => Jon Snow
        )
    [tags] => Array
        (
            [0] => Array
                (
                    [name] => New
                )
        )
    [comments] => Array
        (
            [0] => Array
                (
                    [text] => This is my first comment
                )
            [1] => Array
                (
                    [text] => This is my second comment
                )
        )
)
```

## Templates and defaults

### Control Defaults

OriginPHP uses bootstrap for its front end, and the defaults for each control are configured accordingly.
If you need to change these you can do by calling `controlDefaults` from your within view.

```php
$this->Form->controlDefaults([
    'text' => ['div' => 'form-group', 'class' => 'form-control']
    ]);
```

Or if you want to change them across the whole app or a particular controller, then set the `controlDefaults` option when loading the helper.

```php
  $this->loadHelper('Form', [
      'controlDefaults'=>[
        'text' => ['div' => 'input-field']
      ]
      ]);
```

### Templates

Depending upon the front end framework you are using, you might need to adjust the default templates, for example wrapping a control in another div or changing how the template formats an error message.

You can create a file in your `config`  which should return an array.

For example create add `config/myform-templates.php`  the following code:

```php
return [
    'control' => '<div class="row"><div class="{class} {type}{required}">{before}{content}{after}</div></div>',
    'controlError' => '<div class="{class} {type}{required} error">{before}{content}{after}{error}</div>',
    'error' => ' <span class="helper-text" data-error="wrong" data-success="right">{content}</span>'
];
```

Then when loading the Form Helper set the templates option, this will replace the default ones with the onces that you have defined.

```php
$this->loadHelper('Form',[
    'templates'=>'myform-templates'
    ]);
```

You can also change individual templates at runtime

```php
$this->Form->templates(['error'=>'<div class="omg">{content}</div>']);
```
