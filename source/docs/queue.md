---
title: Queue - Background Jobs
description: Queue Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Queue

You can easily queue schedule background jobs, the OriginPHP queue system works supports both MySQL and Redis backends.

## Configuring Queues

You must setup the `default` configuration for the queue, and if you are going to be unit testing the jobs then you will need to create a `test` configuration, which will be used when running tests.

### Database

To use the database engine, you will need to create the queue table

```linux
$ bin/console db:schema:load queue
```

The settings for Queue can be found in `config/queue.php`, by default the `Database` engine is used with the default connection. When you test queues, this will automatically be switched to your test configuration.

```php
// config/queue.php
use Origin\Job\Engine\DatabaseEngine;

return [
    'default' => [
        'className' => DatabaseEngine::class,
        'connection' => 'default'
    ]
];
```

Options are

- `connection`: The name of the database connection to use. default is `default`;

### Redis

This is how you would configure Redis

```php
// config/queue.php
use Origin\Job\Engine\RedisEngine;

return [
    'default' => [
        'className' => RedisEngine::class,
        'host' => '127.0.0.1',
        'port' => 6379
    ]
];
```

Options are

- `host`: default is `127.0.0.1` however if you are using Docker, then it would something like `redis`
- `port`: default: `6379`

See [Dockerized Development Environment](/docs/development/dockerized-development-environment), on how to use `Redis` in your docker container.

## Creating Jobs

To create a Job and its test file run the following command

```linux
$ bin/console generate job SendWelcomeEmail
```

For more information on code generation see the [code generation guide](/docs/development/code-generation).

Jobs are stored in the `app/Job` folder and must have the `execute` method, any arguments you pass during dispatching will be passed along here.

```php
namespace App\Job;
use App\Job\ApplicationJob;
use Origin\Mailer\Email;
use Origin\Model\Entity;

class SendWelcomeEmailJob extends ApplicationJob
{
    protected $queue = 'notifications';
    // strtotime compatible string
    protected $wait = '+5 minutes'; // wait time before sending new job
    protected $timeout = 60;

    protected function initialize(): void
    {
        $this->onError('errorHandler');
    }

    protected function execute(Entity $user): void
    {
        $Email = new Email();
        $Email->to($user->email)
            ->from('do-not-reply@originphp.com')
            ->subject('Welcome ')
            ->template('welcome')
            ->set(['first_name' => $user->first_name])
            ->send();
    }

    protected function errorHandler(\Exception $exception): void
    {
        $this->retry([
            'wait' => '+30 minutes', // how long to wait before retry
            'limit' => 3 // maxium retry attempts
            ]);
    }
}
```

The `onError` callback is triggered when an error occurs such as an exception that was thrown, there you can create logic to retry the job or only retry the job if certain exceptions are raised etc.

It common for Jobs to carry out maintenance on the database, in the `initialize` method you can load any models that you need.

```php
namespace App\Job;
use App\Job\ApplicationJob;

class ResetUserCreditsJob extends ApplicationJob
{
    protected $queue = 'monthly';

    protected function initialize(): void
    {
        $this->loadModel('User');
    }

    protected function execute(): void
    {
        $this->User->resetCredits();
    }
}
```

### Callbacks

> The execute and onSuccess methods are not defined so you can set this with the correct types and names

- `initialize`: this is called when the job created for dispatching
- `startup`: this is called before the `execute` method
- `execute`: this is called when the job is dispatched using the arguments you passed with the `dispatch` or `dispatchNow` method.
- `shutdown`: this is called after the `execute` method

You can also register callbacks when a job is queued, the arguments will be passed to these registered callbacks.

```php
$this->beforeQueue('methodName');
$this->afterQueue('methodName');
```

Callbacks can also be registered before and after a job is dispatched, these will be called after `startup` and before `shutdown`.

```php
// startup
$this->beforeDispatch('methodName');
$this->afterDispatch('methodName');
$this->onSuccess('methodName');
// shutdown
```

You can also handle errors by registering a callback for on the `onError` event, this will pass the exception
to the method.

```php
$this->onError('methodName');
```

Here is an example Job that if it runs successfully, it will call another job, if it fails it will retry a maximum of 3 times waiting 30 minutes in between each try.

```php
class SendIntroEmailJob extends ApplicationJob
{
    protected function initialize(): void
    {
        $this->onError('sendAgain');
        $this->onSuccess('sendFollowUp');
    }

    protected function execute(Entity $user): void
    {
        $Email = new Email();
        $Email->to($user->email)
            ->from('do-not-reply@originphp.com')
            ->subject('Introduction ')
            ->template('introduction')
            ->set(['first_name' => $user->first_name])
            ->send();
    }

    protected function sendFollowUp(Entity $user): void
    {
        (new SendFollowUpEmailJob())->dispatch($user);
    }

    protected function sendAgain(\Exception $exception): void
    {
        $this->retry([
            'wait' => '+30 minutes', // how long to wait before retry
            'limit' => 3 // maxium retry attempts
            ]);
    }
}
```

## Dispatching Jobs

To dispatch a Job to the queue

```php
use App\Job\SendWelcomeEmailJob;
(new SendWelcomeEmailJob())->dispatch($user);
```

To dispatch the Job immediately

```php
(new SendWelcomeEmailJob())->dispatchNow($user);
```

To schedule a job for a particular time (or using a delay)

```php
(new SendWelcomeEmailJob())->schedule('+ 10 minutes')->dispatch($user);
```

You can also pass the wait and queue options to the constructor

```php
$options = [
    'wait' => 'tomorrow', // strtotime compatible string (same as calling schedule)
    'queue' => 'a-different-queue'
];
(new SendWelcomeEmailJob($options))->dispatch($user);
```

## Worker

OriginPHP comes with its own worker,to run the queue worker on the `default` queue, which will process the pending jobs in the queue and then exit.

```linux
$ bin/console queue:worker
```

To run the queue worker using a different queue connection

```linux
$ bin/console queue:worker --connection=not-default
```

To run the queue worker on different or multiple queue(s)

```linux
$ bin/console queue:worker notifications maintenence
```

To run the queue work in daemon mode, you can use `CTRL+c` to quit.

```linux
$ bin/console queue:worker -d
```

You can also set it to sleep a certain amount of seconds when there are no jobs when running the work as a daemon.

```linux
$ bin/console queue:worker -d -sleep=30
```

### Scheduling Using Cron

On Ubuntu to setup cron tab for the `www-data` user type in the following command:

```linux
$ sudo crontab -u www-data -e
```

Then add the following line, assuming the source code is in the folder `/var/www/app.mydomain.com`.

```
*/5 * * * * cd /var/www/app.mydomain.com && bin/console queue:worker notifications
```

### Using Supervisor

You can use Supervisor to manage the `queue:worker` daemon , it will restart the worker if it shuts and keep alive a certain number of processes.

```linux
sudo apt-get install supervisor
```

Create the `queue-worker.conf` configuration file in `/etc/supervisor/conf.d` using a text editor such `nano` or `vi`.

This example will run 3 processes of the worker

```
[program:worker]
process_name=%(program_name)s_%(process_num)02d
command=/var/www/app.mydomain.com/bin/console queue:worker -d
autostart=true
autorestart=true
user=www-data
numprocs=3
redirect_stderr=true
stdout_logfile=/var/www/app.mydomain.com/logs/worker.log
```

Once you have created the configuration file

```linux
sudo supervisorctl reread
sudo supervisorctl update
```

Then to start it up

```linux
sudo supervisorctl start worker:*
```

For more information see the [Supervisor documentation](http://supervisord.org/index.html).

## Installing Redis

### Redis

**To install Redis in the Docker container**

See [Dockerized Development Environment](/docs/development/dockerized-development-environment), on how to use `Redis` in your docker container.

**To install Redis on a Ubuntu/Debain based server**

```php
pecl install redis
sudo echo 'extension=redis.so' >> /etc/php/7.4/apache2/php.ini
sudo echo 'extension=redis.so' >> /etc/php/7.4/cli/php.ini
```
