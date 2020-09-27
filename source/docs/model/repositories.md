---
title: Model Repository
description: Model Repository Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Repository

To keep logic that has nothing to do with data persistence from bloating up your models, aka fat Models, you can use the OriginPHP `Repository` which is based upon the [Repository Pattern](https://martinfowler.com/eaaCatalog/repository.html).

A good candidate for this example is a User `Model`, since this always gets really bloated.

The first thing to do is to create a `Repository` class using the plural name of the Model, this enables the lazy loading of the model which can be used.

```linux
$ bin/console generate respository Users
```

This will create two files

```linux
[ OK ] /var/www/app/Model/Repository/UsersRepository.php
[ OK ] /var/www/tests/TestCase/Model/Repository/UsersRepositoryTest.php
```

## How To use

Take the code that queries, or saves and deletes from your `Models` and place these in their own `Repository`, the `Model` will be auto-detected using the name of the `Repository`.

```php
use Origin\Model\Repository\Repository;
use App\Model\User;
use Origin\Model\Entity;

class UsersRepository extends Repository
{
    public function findListActiveUsers(): array
    {
        $conditions = ['active' => true];
        return $this->User->find('list',['conditions' => $conditions]);
    }

    public function save(Entity $user): bool
    {
        if(!$this->User->save($user)){
            return false;
        }

        if ($user->created()) {
            Log::write('info', 'User registered', [
                'channel' => 'users',
                'username' => $user->username
            ]);

            (new WelcomeEmailMailer())->dispatchLater($user);

            (new SlackNotificationService()->dispatch($user));
        }
        return true;
    }
}
```

When creating the Repository any arguments passed to the constructor will also be passed to the `initialize` hook, so you can use this for dependency injection.

```php
class UsersRepository extends Repository
{
    protected initialize(SomeModel $model,array $config = []): void
    {
        $this->SomeModel = $model;
        $this->config = $config;
    }
}

```

## Loading the Repository

Open your Controller, in the `initialize` method create the `Repository`, then change the save function to use the `Repository` instead of the `Model`.

```php
use App\Model\Repository\UserRepository;

class UsersController extends ApplicationController
{
    protected function initialize(): void
    {
        parent::initialize();
        $this->Users = new UsersRepository();
    }

    public function signup()
    {
        $user = $this->User->new();

        if ($this->request->is(['post'])) {
            // Safeguard request data (model)
            $user = $this->User->new($this->request->data(), [
                'fields' => ['email','username','password','full_name'],
            ]);

            // Use the Users Repository
            if ($this->Users->save($user)) {
                $this->Flash->success(__('You have been signed up'));

                return $this->redirect('/');
            }
            $this->Flash->error(__('Error signing up'));
        }

        $this->set(compact('user'));
    }
}
```