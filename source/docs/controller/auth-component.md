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

    public function initialize()
    {
        parent::initialize(); // !Important whenever you use a callback or initialize method
        $this->loadComponent('Auth',$options);
        ...
    }

```

The default config for the `AuthComponent`.

- *authenticate*: Supports `Form` and `Http`
- *loginAction*: This is the login action for the Form
- *loginRedirect*: This is where users are taken too when they login
- *logoutRedirect*: This is where users are taken too when they logout
- *model*: This the model used, default User
- *fields*: This is to configure the `username` field default is `email` and the password field in the model which called by the same name. The `api_token` is the name of the field where you will store an api token for the api authentication method.
- *unauthorizedRedirect*: By default the user is redirected if the credentials are incorrect, however if you set to false then an exception is thrown.
- *authError*: The message to be displayed to the user

```php
     $config  = [
            'authenticate' => ['Form','Http'], // Form and Http supported
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
            'fields' => ['username' => 'email', 'password' => 'password','api_token'=>'api_token'],
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

The the default password hasher for OriginPHP uses the php password_hash function, which is very secure, and has been wrapped in its easy to use function.

```php
$password = hashPassword($password); // same as  password_hash($password, PASSWORD_DEFAULT); 
```

So when a user signs up or changes their password you will need to hash the password, this will normally  be done in your user model, this will help keep your controller slim.

```php
use Origin\Utility\Security;
class User extends AppModel
{
    public function beforeSave(Entity $entity, array $options = [])
    {
        if(!parent::beforeSave($entity,$options)){
            return false;
        }

        if(!empty($entity->password)){
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

Add a column to your users table called api_token, you can call it something else but you will have to change the authentication configuration.

In the controller that you want to enable the API authentication method add the following code.

```php
    public function initialize()
    {
        parent::initialize(); // !Important whenever you use a callback or initialize method
        $this->loadComponent('Auth',[
            'authenticate' => ['Api']
        ]);
        ...
    }
```

You can use the `uid` or `uuid` functions to generate secure api tokens.

```php
$user->api_token = uid(40); // bbd7cebc6274d5ec7aabbdbaa4885e0b2f75d091
$user->api_token = uuid(); // 1546d376-8b3b-4ce9-b763-f95b8cbbeb82
```

> Remember you will need to set the request type to json. 