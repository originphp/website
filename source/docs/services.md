---
title: Services
description: Services Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Services (Service Objects)

Services are reusable business logic, that are used to keep both your Controllers and Models skinny whilst making code simpler, easier to test and maintain. Services follow the `dependency injection` and `command` patterns, and the Service only does just one thing, in other words it follows the `single responsibility principle`. It is both a very simple concept and object, but it is very powerful.

Services are a specific business action including workflow and should be named with a verb explaining what it should do e.g. `SignupUserService` or `StripeChargeCardService`. Services should be saved to `app/Service`, and are typically called from a `Controller`, `Command` or `Job`. You can also group similar services into their own sub folder.

For example

```
|-- app
|   |-- Service
|   |   -- Stripe
|   |       |-- NewCustomerService
|   |       |-- ChargeCreditCardService
|   |       |-- RefundCreditCardService
```

When you create the instance for the Service you inject dependencies in the constructor arguments, these will be passed to the `initialize` method. An example of this, in the controller you create a new service and pass the User model. The Service requires two methods , `initialize` and `execute`.

Any dependencies that you passed when constructing will be passed to the initialize method. The execute method is where the logic is stored. Services also work with `startup` and `shutdown` callbacks which are triggered when you dispatch the service. Any other methods should be private (or at least protected).

Use the generate command to create the Service and its test for you

```linux
$ bin/console generate service CreateNewUser
```

For more information on code generation see the [code generation guide](/docs/development/code-generation).

An example Service might look this, remember to store any dependencies that you injected as protected
properties so you can use them later in the execute method.

```php
namespace App\Service;
use App\Service\ApplicationService;
use Origin\Exception\Exception;
use Origin\Log\Log;
use Origin\Service\Result;
use App\Job\SendWelcomeEmail;

/**
 * @method Result dispatch(array $params)
 */
class CreateNewUserService extends ApplicationService {

    protected $User = null;

    protected function initialize(User $user): void
    {
        $this->User = $user;
    }

    # Use any type or number of params and it should return Result object or nothing.
    protected function execute(array $params): ?Result
    {
        $user = $this->User->create($params);
        if($this->User->save($user)){

            mkdir(ROOT . DS . 'data' . DS . $user->id, 0700);

            Log::info('User created',[
                'username' => $user->username
                ]);

            $this->sendWelcomeEmail($user);

            return new Result(['data' => $user]);
        }

        return new Result([
            'error' => [
                'code' => 400,
                'message' => 'Validation error',
                'type' => 'validation-error',
                'validationErrors' => $user->errors()
            ]
        ]);
    }

    private function sendWelcomeEmail(Entity $user): void
    {
        (new SendWelcomeEmailJob())->dispatch($user);
    }
}
```

Then to dispatch it, which will call `startup`, `execute` and then `shutdown`, and then return the result.

```php
$service = new CreateNewUserService($this->User);
$result = $service->dispatch([
    'name' => 'Jon Snow',
    'email' => 'jon@example.com'
]);
```

Or in one line

```php
$result = (new CreateNewUserService($this->User))->dispatch([
    'name' => 'Jon Snow',
    'email' => 'jon@example.com'
]);
```

## Service Results

When running services, returning `true` or `false` is not always enough. Some services, might have different types of errors or if successful it might need to return some data as well, such as a record that was created.

The Result object is based upon the [Google JSON Style Guide](https://google.github.io/styleguide/jsoncstyleguide.xml), so you should pass an array with a key `error` if something went wrong, and `data` with the payload if everything is okay.

The Service `dispatch` method returns either null or a Result object depending upon what the execute returns. The Result object provides a consistent way for dealing with this.

You can create a Service Result Object using the result method

```php
$result = $this->result([
    'data' => $user
]);
```

This just creates the result and passes the array when creating the Result object.

```php
use Origin\Service\Result;
$result = new Result([
        'error' => [
            'message' => 'Invalid Credit Card Number',
            'code' => 500,
        ],
    ]);
```

The result will look like this:

```php
/*
Origin\Service\Result Object
(
    [error] => Array
        (
            [message] => Invalid Credit Card Number
            [code] => 500
        )
)
*/
```

## Working with results

To check if a result does not have a `error` key

```php
$result->success();
```

To work with the payload data

```php
$data = $result->data();
$user = $result->data('user');
```

To work with the error data

```php
$error = $result->error();
$code = $result->error('code');
```

## Callbacks

- `initialize`: this is called when the Service object is created
- `startup`: this is called before the `execute` method
- `execute`: this is called when the Service is dispatched using the arguments you passed to the dispatch method.
- `shutdown`: this is called after the `execute` method
