---
title: Concerns - Controller
description:  Concerns - Model Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Concerns

Concerns are `traits` which you use to share code between Controllers, these go in the `app/Http/Controller/Concern` folder.

The first thing to do is generate some code

```linux
$ bin/console generate concern_controller UserLimit
```

This will create two files

```
[ OK ] /var/www/app/Http/Controller/Concern/UserLimit.php
[ OK ] /var/www/tests/TestCase/Http/Controller/Concern/UserLimitTest.php
```

Then we will adjust add some functionality to demonstrate

```php
namespace App\Http\Controller\Concern;

trait UserLimit
{
    /**
     * Initialization method, here you can register callbacks or setup your controller
     *
     * @return void
     */
    protected function initializeUserLimit() : void
    {
        $this->loadModel('User');
        $this->beforeAction('checkCount');
        $this->afterAction('updateCount');
    }

    public function throwMonthlyLimitException(){
          throw new ForbiddenException('You have reached your monthly limit');
    }
    
    protected function updateCount(){
        if($this->Auth->isLoggedIn()){
            $this->User->increment('pages_viewed',$this->Auth->user('id'));
        }
    }

    protected function checkCount(){
        if($this->Auth->isLoggedIn()){
            $user = $this->User->get($userId);
            if($user->pages_viewed < $user->monthly_limit){
                return;
            }
            $this->throwMonthlyLimitException();
        }
       return null;
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

## Initializer Trait

Controllers have the `InitializerTrait` enabled, enables you to use an initialization method on traits. See the [InitializerTrait guide](/docs/initializer-trait) for more information.

## Registering Additional Callbacks

As the `Concern` is attached to the `Controller` you can use the controller functions to register additional callbacks on a number of events, `beforeAction`,`afterAction`,`beforeRender` and `beforeRedirect`.

```php
$this->beforeAction('checkCount');
$this->afterAction('updateCount');
$this->beforeRender('cacheView');
$this->beforeRedirect('doSomethingIamOutOfIdeas');
```

## Disabling callbacks

To disable a callback from within the `Controller`:

```php
$this->disableCallback('checkCount');
```

Then to re-enable:

```php
$this->enableCallback('checkCount');
```