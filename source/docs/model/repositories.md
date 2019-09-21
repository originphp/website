---
title: Model Repository
description: Model Repository Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Repository

To keep logic that has nothing to do with data persistence from bloating up your models, aka fat Models, you can use the OriginPHP Repository which is based upon the Repository Pattern.

A good candidate for this example is a User model, since this always gets really bloated.

The first thing to do is to create a Repository class using the plural name of the Model.

```linux
$ bin/console generate respository Users
```

This will create two files

```linux
[ OK ] /var/www/app/Model/Repository/UsersRepository.php
[ OK ] /var/www/tests/TestCase/Model/Repository/UsersRepositoryTest.php
```

## How To use

Typically you will have either code in the Controller or in a callback to do something once a new User registers, you are going to take that code out and put this in the Repository. Note it is not just for callbacks, basically any code that you would put in the model, you now put in this new layer.

```php
use Origin\Model\Repository\Repository;
use App\Model\User;
use Origin\Model\Entity;

class UsersRepository extends Repository
{
    public function save(Entity $user)
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
    }
}
```

## Loading the Repository

Open your Controller, in the `initialize` method create the `Repository`, then change the save function to use the `Repository` instead of the `Model`.

```php
use App\Model\Repository\UserRepository;

class UsersController extends AppController
{
    public function initialize()
    {
        parent::initialize();
        $this->UsersRepository = new UsersRepository();
    }

    public function signup()
    {
        $user = $this->User->new();

        if ($this->request->is(['post'])) {
            // Safeguard request data
            $user = $this->User->new($this->request->data(), [
                'fields' => ['email','username','password','full_name'],
            ]);

            if ($this->UsersRepository->save($user)) {
                $this->Flash->success(__('You have been signed up'));

                return $this->redirect('/');
            }
            $this->Flash->error(__('Error signing up'));
        }

        $this->set(compact('user'));
    }
}
```