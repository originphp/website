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

The settings for Queue can be found in `config/queue.php`, by default the `Database` engine is used with the default connection. When you test queues, this will automatically be switcehd to your test configuration.

```php
use Origin\Job\Queue;
Queue::config('default', [
    'engine' => 'Database'
]);
```

Options are

- `connection`: The name of the database connection to use. default is `default`;

### Redis

This is how you would configure Redis

```php
use Origin\Job\Queue;
Queue::config('default', [
    'engine' => 'Redis',
    'host' => '127.0.0.1',
    'port' => 6379
]);
```

Options are

- `host`: default is `127.0.0.1` however if you are using Docker, then it would something like `redis`
- `port`: default: `6379`

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
    public $queue = 'notifications';
    // strtotime compatible string
    public $wait = '+5 minutes'; // wait time before sending new job
    public $timeout = 60;

    public function execute(Entity $user) : void
    {
        $Email = new Email();
        $Email->to($user->email)
            ->from('do-not-reply@originphp.com')
            ->subject('Welcome ')
            ->template('welcome')
            ->set(['first_name'=>$user->first_name])
            ->send();
    }

    public function onError(\Exception $exception)
    {
        $this->retry([
            'wait' => '+30 minutes', // how long to wait before retry
            'limit' => 3 // maxium retry attempts
            ]);
    }
}
```

The `onError` method is called when an error occurs such as an exception that was thrown, there you can create logic to retry the job or only retry the job if certain exceptions are raised etc.

It common for Jobs to carry out maintenance on the database, in the `initialize` method you can load any models that you need.

```php
namespace App\Job;
use App\Job\ApplicationJob;

class ResetUserCreditsJob extends ApplicationJob
{
    public $queue = 'monthly';

    public function initialize() : void
    {
        $this->loadModel('User');
    }

    public function execute() : void
    {
        $this->User->resetCredits();
    }
}
```

### Callbacks

> The execute and OnSuccess methods are not defined so you can set this with the correct types and names

- `initialize`: this is called when the job created for dispatching
- `startup`: this is called before the `execute` method
- `execute`: this is called when the job is dispatched using the arguments you passed with the `dispatch` or `dispatchNow` method.
- `shutdown`: this is called after the `execute` method
- `onError`: this is called when an exception is caught (job fails)
- `onSuccess`: if the job ran without issues then this will be called with the same arguments that you passed when constructing the job.

Here is an example Job that if it runs successfully, it will call another job, if it fails it will retry a maximum of 3 times waiting 30 minutes in between each try.

```php
class SendIntroEmailJob extends ApplicationJob
{
    public function execute(Entity $user) : void
    {
        $Email = new Email();
        $Email->to($user->email)
            ->from('do-not-reply@originphp.com')
            ->subject('Introduction ')
            ->template('introduction')
            ->set(['first_name'=>$user->first_name])
            ->send();
    }

    public function onSuccess(Entity $user) : void
    {
        (new SendFollowUpEmailJob())->dispatch($user);
    }

    public function onError(\Exception $exception) : void
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

You can use Supervisor to manage the `queue:worker` daemon , it will the worker if it shuts and keep alive a certain number of processes.

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

First add the following to the `docker-compose.yml` file, this will load the Redis image.

```
  redis:
      image: redis
```

In the `Dockerfile` add the following lines to install and enable the Redis PHP extension.
```
RUN pecl install redis
RUN echo 'extension=redis.so' >> /etc/php/7.2/apache2/php.ini
RUN echo 'extension=redis.so' >> /etc/php/7.2/cli/php.ini
```
Then run the build command in docker-compose.

```linux
docker-compose build
```

Then set the host to `redis` in your cache config.

**To install Redis on a Ubuntu/Debain based server**

```php
pecl install redis
sudo echo 'extension=redis.so' >> /etc/php/7.2/apache2/php.ini
sudo echo 'extension=redis.so' >> /etc/php/7.2/cli/php.ini
```