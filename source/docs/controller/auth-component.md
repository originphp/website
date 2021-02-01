---
title: Auth Component
description: Auth Component Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Auth Component

The Auth Component makes it easy to secure your application by requiring login.

You enable login via forms and/or http requests. By default only forms is enabled.

To load and enable the `AuthComponent`, in your app controller initialize method add the following

```php
protected function initialize(): void
{
    parent::initialize(); // !Important whenever you use a callback or initialize method
    $this->loadComponent('Auth',$options);
    ...
}
```

The default config for the `AuthComponent`.

- *authenticate*: Supports `Form` , `Http`, `Api`, and `Controller`
- *loginAction*: This is the login action for the Form
- *loginRedirect*: This is where users are taken too when they login
- *logoutRedirect*: This is where users are taken too when they logout
- *model*: This the model used, default User
- *fields*: This is to configure the `username` field default is `email` and the password field in the model which called by the same name. The `api_token` is the name of the field where you will store an api token for the api authentication method.
- *unauthorizedRedirect*: By default the user is redirected if the credentials are incorrect, however if you set to false then an exception is thrown.
- *authError*: The message to be displayed to the user

```php
$config  = [
    'authenticate' => ['Form','Http'], // From,Http,Api,Controller
    'loginAction' => [
        'controller' => 'Users',
        'action' => 'login',
        'plugin' => null,
    ],
    'loginRedirect' => [
        'controller' => 'Users',
        'action' => 'index',
        'plugin' => null,
    ],
    'logoutRedirect' => [
        'controller' => 'Users',
        'action' => 'login',
        'plugin' => null,
    ],
    'model' => 'User',
    'fields' => ['username' => 'email', 'password' => 'password','api_token' => 'api_token'],
    'scope' => [], // Extra conditions for db . e.g users.active=1;
    'unauthorizedRedirect' => true, // If false no redirect just exception e.g cli stuff
    'authError' => 'You are not authorized to access that location.',
]
```

## Authentication using Username and Password fields

In the controller add a method for the login, first we need to identify the user, if the user is authenticated then it will return a User Entity. Then if the user is returned you can modify any data then use the `login` method, which converts the User into an array and stores in the Session, which means the the User in.

```php
public function login()
{
    if ($this->request->is('post')) {
        $user = $this->Auth->identify();
        if ($user) {
            $this->Auth->login($user);

            return $this->redirect($this->Auth->redirectUrl());
        }
        $this->Flash->error(__('Incorrect username or password.'));
    }
}
```

When you need to access the logged in user info, you call the `user` method, if you do not pass a name
of a field, then it will return an array of the User information.

```php
$user = $this->Auth->user();
```

Alternatively, you get an individual value from the user array by passing a key.

```php
$email = $this->Auth->user('email');
```

The the default password hasher for OriginPHP uses the php password_hash function, which is very secure. To hash a password using the default password hasher:

```php
use Origin\Security\Security;
Security::hashPassword($entity->password);
```

So when a user signs up or changes their password you will need to hash the password, this will normally  be done in your user model, this will help keep your controller slim.

```php
use Origin\Security\Security;
class User extends ApplicationModel
{
    protected function initialize(array $config): void 
    {
        $this->beforeSave('hashPassword');
    }

    protected function hashPassword(Entity $entity, ArrayObject $options): bool
    {
        if ($entity->isDirty('password')) {
            $entity->password = Security::hashPassword($entity->password);
        }

        return true;
    }
}
```

In some controllers you might to want to allow certain actions to not require authentication, in this case you can pass and array of allowed actions

```php
$this->Auth->allow(['reset_password','verify_email']);
```

Sometimes you want to know if the User is logged in, to do this use the `isLoggedIn` method.

```php
if($this->Auth->isLoggedIn()){
    // do something
}
```

## Authentication using API Token

Add a column to your users table called api_token, you can call it something else but you will have to change the `AuthComponent` configuration for fields.

### Configuration

In the controller that you want to enable the API authentication method add the following code.

```php
class ApiController extends ApplicationController
{
    protected function initialize(): void
    {
        $this->loadComponent('Auth',[
            'authenticate' => ['Api']
        ]);
    }
}
```

Then in your `config/routes.php` add the following route, assuming your controller is called Api, this will ensure that errors are rendered in json automatically.

```php
Router::add('/api/:action/*', ['controller' => 'Api','type' => 'json']);
```

Then your API requests will look like this 

```
GET http://localhost:8000/api/dosomething/12345?api_token=3905604a-b14d-4fe8-906e-7867b39289b7
```

### Generating API tokens

You can use `Security::random` or `Security::uuid` to generate secure api tokens.

```php
use Origin\Security\Security;
$user->api_token = Security::random(40); // bbd7cebc6274d5ec7aabbdbaa4885e0b2f75d091
$user->api_token = Security::uuid(); // 1546d376-8b3b-4ce9-b763-f95b8cbbeb82
```

### Controller

To add an additional layer, you can use the `Controller`.

In your controller create a method called `isAuthorized`.

```php
public function isAuthorized(array $user)
{
    if($user['admin'] === 1){
        return true;
    }
    return false;
}
```