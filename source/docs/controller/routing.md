---
title: Routing
description: Routing Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Routing

When requests come they are routed to the correct controller. You configure routes in the `config\routes.php` file.

To add a route you use the Router add method.

```php
Router::add($route,$options);
```

Lets look at some real examples.

You prefer a user does not see the controller and action in the url , this example will take all requests to `/login` and send it to the users controller and call the action `login`.

```php
Router::add('/login', ['controller' => 'Users', 'action' => 'login']);
```


You want all requests to / to display a page using the pages controller.

```php
Router::add('/', ['controller' => 'Pages', 'action' => 'display', 'home']);
```

You want to parse the url in a certain way using variables. `:controller` for controller and `:action` for action. This is the default routing that is used in the framework, you can remove this and then only setup routes for what you want.

```php
Router::add('/:controller/:action/*');
```

For example if you only wanted to route for the posts controller.

```php
Router::add('/posts/:action/*',['controller' => 'Posts']);
```

You can use the same to show a different controller in the url.

```php
Router::add('/posts/:action/*', ['controller' => 'BlogPosts']);
```

When you create and use plugins you will need to setup a route for this to work, again this quite straight forward. Lets say you created a demo plugin, this is how you would setup the route.

```php
Router::add('/demo/:controller/:action/*', ['plugin' => 'Demo']);
```

## Request types

You can set specific request types in the route as well.

```php
Router::add('/api/:controller/:action/*', ['type' => 'json']);
```

## Prefixes

Sometimes you might need an administration section where privileged users can make changes to data or have access to certain features.

Lets assume you wanted to create an `admin` prefix.

Add the following route to top of `config/routes.php`

```php
Router::add('/admin/:controller/:action/*', ['prefix' => 'Admin']);
```

Create your `Controller` in `app/Http/Controller/Admin` folder.

```php
namespace App\Http\Controller\Admin;

use App\Http\Controller\ApplicationController;

class UsersController extends ApplicationController
{
    public function edit()
    {

    }
}
```

Now create the view folder `app\Http\View\Admin` for this prefix, and create a sub folder `Users` for this `Controller`.

So the `View` file for this new controller would be `app\Http\View\Admin\Users\edit.ctp`.

Any links that you create using the `HtmlHelper` or if you use the `Controller::redirect` function it will automatically add the the `Admin` prefix. Sometimes you might want to link or redirect to somewhere outside the prefix, in this case, set `prefix` to `false`.

For example

```php
$this->Html->link('somewhere',[
    'controller' => 'Foos',
    'action' => 'dosomething',
    'prefix' => false // set to false
    ]);
```