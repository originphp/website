---
title: Task Scheduler
description: Task Scheduler for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Task Scheduling

You can schedule a task to execute a command, dispatch a job, use a closure or invokeable object.

## Create a Task

Use the generator to create a task

```bash
$ bin/console generate task Backup
```

This will create the task in the `app/Task` folder.

```php
declare(strict_types = 1);
namespace App\Task;

use Origin\Schedule\Schedule;
use Origin\Schedule\Task;

class BackupTask extends Task
{
    protected function handle(Schedule $schedule): void
    {
        $schedule->command('bin/console chronos --compression=bzip2')
            ->daily()
            ->at(23);
    }
}
```

## Scheduling

To schedule a command

```php
$schedule->command('bin/console chronos --compression=bzip2')
    ->weekly();
```

To schedule a Job

```php
$arguments = [];
$schedule->job(new ClearRecentListJob,$arguments)
    ->monthly();
```

To schedule a task using a `closure` or PHP class with the \_\_invoke method

```php
$arguments = [];
$schedule->call(function () {
        return true;
    })->daily();
```

## Setup CRON

Create a cron job to run schedule command every minute.

```bash
$ sudo crontab -u www-data -e
```

If you are using this as part of the OriginPHP framework

```bash
* * * * * cd /var/www/app.example.com && bin/console schedule:run
```

If you have installed as an individual package, then the command to run will be in the `vendor/bin` folder

```bash
* * * * * cd /var/www/app.example.com && vendor/bin/schedule:run
```

## Schedule Frequency methods

The following methods allow you to set the frequency for the schedule.

- everyMinute
- every5Minutes
- every10Minutes
- every15Minutes
- every20Minutes
- every30Minutes
- hourly
- daily
- weekly
- monthly
- quarterly
- yearly
- sundays-saturdays
- on( day number ) e.g. sunday = 0
- at(hour, minute)
- weekdays
- between(start,end)
- cron - this accepts a cron expression, e.g. '0 0 \* \* 1-5'

## Output

You can save output from a `command`.

```php
$scheduler->command('ls', ['-lah'])
    ->daily()
    ->output(storage_path('output.txt'));
```

If you prefer to append the output to a file, then pass `true` in the second argument of the output method.

```php
$scheduler->command('ls -lah')
    ->cron('* * * * 1')
    ->output(storage_path('output.txt'),true);
```

## Background

Commands will be run in the foreground by default, if you want the command to run in the background then use the `background` method.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->background();
```

## Limit

By default scheduled tasks will run even if the previous instance is still running, you can limit the maximum amount of instances that can be run at a single time using the `limit` method.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->limit(1);
```

## Spawning multiple processes

You can spawn multiple processes of a task using the `processes` method.

```php
$scheduler->command('bin/console mailbox:check')
    ->every5Minutes()
    ->background(),
    ->processes(3)
```

## Maintenance Mode

If you have enabled maintenance mode, then any events will not be run. However, if you need to run certain events when maintenance mode is enabled, then you can do so like this.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->evenInMaintenanceMode();
```

## Conditions

### When

You can add a condition so that the task is only run if the condition(s) return true.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->when(function () {
        return true;
    });
```

### Skip

You can set a condition to skip tasks if the closure returns true.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->skip(function () {
        return true;
    });
```

## Hooks

### Before

The `before` hook is run before the task is executed

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->before(function () {
        return true;
    });
```

### After

The `after` hook is run after the task is executed

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->after(function () {
        return true;
    });
```

### onSuccess

The `onSuccess` hook is the exit code of the command was `0` or the callable did not return `false`.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->onSuccess(function () {
        // do something
    });
```

### onError

The `onError` hook if there was an error executing the command or the callbable returned `false`.

```php
$scheduler->command('backup --full')
    ->cron('* * * * 1')
    ->onError(function () {
        // do something
    });
```
