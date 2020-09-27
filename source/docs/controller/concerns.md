---
title: Concerns - Controller
description:  Concerns - Model Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Concerns

A `Concern` is a trait which you can use to share code between `Controllers` that go in the `app/Http/Controller/Concern` folder. Do not use `Concerns` to reduce code in your `Controllers` that is an anti-pattern, only use a `Concern` when you need to share code across multiple `Controllers`.

The first thing to do, is generate the classes

```linux
$ bin/console generate concern_controller UserLimit
```

This will create two files

```
[ OK ] /var/www/app/Http/Controller/Concern/UserLimit.php
[ OK ] /var/www/tests/TestCase/Http/Controller/Concern/UserLimitTest.php
```

For more information on code generation see the [code generation guide](/docs/development/code-generation).

## How to Use

Since `Concerns` are  `traits`, you can add to any `Controller` like this

```php
use App\Http\Controller\Concern\UserLimit;

class ArticlesController extends ApplicationController
{
  use UserLimit;
}
```

## Callbacks

You can use callbacks with `Concerns`, the initialization method for `Concerns` starts with initialize then has the trait name (without the Trait suffix), for example `initializeUserLimiter`.

### Initialization

When the `Controller` is created, the initialization method for the `Concern` is called, if it exists, from here you can register callbacks or run other functions if needed.

```php
namespace App\Http\Controller\Concern;

trait MyConcern
{
    protected function initializeMyConcern(): void
    {
    }
}
```

### Registering Callback from the Concern

To be able to use `Controller` callbacks with your `Concern` you must register them, using the `Controller` methods `beforeAction`, `afterAction`, `beforeRedirect` or `beforeRender`.

```php
protected function initializeMyConcern(): void
{
    $this->beforeAction('checkRequest');
}
protected function checkRequest()
{
    // your code here
}
```

### Available Callback Events

The available callback events are

- `beforeAction` - This is called after the controller `startup` callback, but before the controller action
- `afterAction` - This is called after the controller action but before the controller `shutdown` callback
- `beforeRender` - Called before the render process starts
- `beforeRedirect` - Called before any redirect takes place

### Disabling callbacks

To disable a callback use the `Controller` method:

```php
$this->disableCallback('checkCount');
```

Then to re-enable:

```php
$this->enableCallback('checkCount');
```

## Controller Concern Example

Here is a random example to demonstrate how you an use callbacks with `Concerns`

```php
namespace App\Http\Controller\Concern;

trait UserLimit
{
    /**
     * Initialization method, here you can register callbacks or setup your controller
     *
     * @return void
     */
    protected function initializeUserLimit(): void
    {
        $this->loadModel('User');
        $this->beforeAction('checkCount');
        $this->afterAction('updateCount');
    }

    public function throwMonthlyLimitException(): void
    {
          throw new ForbiddenException('You have reached your monthly limit');
    }

    protected function updateCount(): void
    {
        if($this->Auth->isLoggedIn()){
            $this->User->increment('pages_viewed',$this->Auth->user('id'));
        }
    }

    protected function checkCount(): void
    {
        if($this->Auth->isLoggedIn()){
            $user = $this->User->get($userId);
            if($user->pages_viewed < $user->monthly_limit){
                return;
            }
            $this->throwMonthlyLimitException();
        }
    }
}
```

Now in any Controller you want to Limit number of pages views

```php
use App\Http\Controller\Concern\UserLimit;
class ArticlesController extends ApplicationController
{
  use UserLimit;
}
```