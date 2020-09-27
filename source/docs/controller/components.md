---
title: Components
description: Components Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Components

`Components` are objects that you can share between `Controllers`. When you create a `Component`, you can use other `Components` within the `Component` and access the current `Controller`. If you are just looking to share code between `Controllers` then you should use [Concerns](/docs/controller/concerns) instead.

## Creating a Component

`Components` are stored in the `App/Http/Controller/Component` folder.

You can run the following command

```linux
$ bin/console generate component Math
```

This will create the `Component` and its test file.

For more information on code generation see the [code generation guide](/docs/development/code-generation).

```php
namespace App\Http\Controller\Component;
use Origin\Http\Controller\Component\Component;

class MathComponent extends Component
{
  public function sum(int $x,int $y) : int
  {
    return $x+$y;
  }

  public function doSomethingWithControler() : void
  {
    $controller = $this->controller(); // get current controller
    $result = $controller->method('xyz');
  }
}
```

After a component is created the component `initialize` method will be called, this is where you can put any code that you need to be executed when a component is created. This is a hook so you don't need to override the `___construct()`.

## Loading Components

To load a component in the controller, you call `loadComponent` from within the `initialize` method so the the callbacks can be executed.

```php
class WidgetsController extends ApplicationController
{
  protected function initialize(array $config) : void
  {
      parent::initialize($config);
      $this->loadComponent('Math');
  }
}
```

## Using Components

 To use a component, you call it from within your controller methods.

```php
class WidgetsController extends ApplicationController
{
  public function doSomething() : int
  {
    return $this->Math->sum(1,2);
  }
}
```

If you want to use a component within a component then you call the `loadComponent` method, the component will then be lazy loaded when you next call it. When you load a component within a component, this component will not have callbacks executed unless the component is already loaded in a controller.

```php
class MathComponent extends Component
{
   protected function initialize(array $config) : void
    {
      $this->loadComponent('Math',$config);
    }
}
```

## Callbacks

There are two callbacks which Components use `startup` and `shutdown`. To use the callbacks, just create a method in your component with the callback name. Both these callbacks must be public since the `Controller` will call these.

### Startup callback

This called after the controller `startup` but before the controller action.

```php
public function startup()
{

}
```

### Shutdown callback

This is called after the controller action but before the controller `shutdown`.

```php
public function shutdown()
{

}
```

## Accessing the request object

If you need to access the request object from within the component.

```php
$request = $this->request();
```

## Accessing the response object

If you need to access the response object from within the component.

```php
$response = $this->response();
```

## Accessing the controller

When working with components, you may need to access the controller, this can be easily done by calling the controller method.

```php
$controller = $this->controller();
```

## Component Configuration

Components work with the `Configurable` package, standardizing and simplifying how you work with configuration. See the [ConfigTrait guide](/docs/configurable) for more information.