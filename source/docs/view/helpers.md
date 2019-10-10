---
title: View Helpers
description: View Helpers Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Helpers

You can create helpers, which are like components, and can contain logic for creating the views. For example we provide a Form helper, which makes working with forms very easy.

## Included Helpers

- [Html Helper](/docs/view/html-helper)
- [Form Helper](/docs/view/form-helper)
- [Date Helper](/docs/view/date-helper)
- [Number Helper](/docs/view/number-helper)
- [Cookie Helper](/docs/view/cookie-helper)
- [Session Helper](/docs/view/session-helper)

## Creating Helpers

Helpers are stored in the `View/Helper` folder of your app. 

To create a `helper`, run the following command

```linux
$ bin/console generate helper Foo
```

This will create the `Helper` and its test files.

For more information on code generation see the [code generation guide](/docs/development/code-generation).

Here is an example of a simple helper:

````php
namespace app\Http\View\Helper;

use Origin\Http\View\Helper\Helper;

class FooHelper extends ApplicationHelper
{
    /**
     * This is called when the Helper is created. You can put any logic here
     * instead of overiding the construct.
     */
    protected function initialize(array $config)
    {
        ...
    }

    public function doSomething($data)
    {
        return 'bar';
    }

}
````

## Loading Helpers

Once you have created the Helper, the next thing to do is to load this in the controller, you can optionally pass an array of options.

```php
class ApplicationController extends Controller
{
    protected function initialize() : void
    {
        $this->loadHelper('Foo',['setting'=>'on']);
    }
}
```

## Using Helpers

Then you can access the Helper from the view

```php
    <h1>Hello world!</h1>
    <?= $this->Foo->doSomething($someData); ?> // outputs bar
```

You can also load Helpers within helpers, in your helper `initialize` method, call the `loadHelper` method. Then you access another helper from within your helper.

```php
protected function initialize(array $config) : void
{
    $this->loadHelper('Session');
}

public function magic()
{
    $secret = $this->Session->read('Magic.tricks.rabbit');
}
```

## Initialize

When a Helper is created, the `initialize` method will be called, this is where you can put any code
that you need to be executed when a Helper is created. This is a hook so you don't need to override the `___construct()`.

## Callbacks

There are two callbacks which Helpers use `startup` and `shutdown`. To use the callbacks, just create a method in your Helper with the callback name.

## Accessing the request object

If you need to access the request object from within the Helper.

```php
$request = $this->request();
```

## Accessing the response object

If you need to access the response object from within the Helper.

```php
$response = $this->response();
```

## Accessing the View

When working with Helpers, you may need to access the view object, this can be easily done by calling the view method.

```php
$view = $this->view();
```

## Helper Configuration

Helpers work with the `ConfigTrait`, standardizing and simplifying how you work with configuration. See the [ConfigTrait guide](/docs/config-trait) for more information.