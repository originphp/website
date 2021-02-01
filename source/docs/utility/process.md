---
title: Process
description: Process Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Process

> This is new in version 3.17

Run commands in sub processes

## Installation

This package is already installed but if you want to use this in your other projects you can install using composer.

```linux
$ composer require originphp/process
```

## Usage

To run a command and wait until its finished

```php
use Origin\Process\Process;
$process = new Process(['ls','-la'])
$result = $process->execute();

$output = $process->output();
$errorOutput = $process->error();
$result = $process->success();
```

You can also pass an array options as the 2nd argument when creating the process object, with any of the following keys

- directory: the directory to execute the command in, default is the current working directory.
- env: an array of key values for environment variables
- output: if TTY is supported output will be sent to screen, default is `false`
- escape: escapes the command, default is `true`

To create a background process

```php
use Origin\Process\BackgroundProcess;
$process = new BackgroundProcess(['backup','/var/www']);
```

You can also pass an options array as the second argument with any of the following keys

- directory: the directory to execute the command in, default is the current working directory.
- env: an array of key values for environment variables
- output: if TTY is supported output will be sent to screen, default is `false`
- escape: escapes the command, default is `true`
- timeout: set the timeout value in seconds, default is `null`

To start the process

```php
$process->start();
```

To wait until the process is finished

```php
$process->wait(); 
```

To stop the process

```php
$process->stop();
```

An example how to run a command asynchronously

```php
use Origin\Process\BackgroundProcess;
$process = new BackgroundProcess(['backup','/var/www'])
$process->start();


$output = '';
while($process->isRunning()){
    $output .= $process->readOutput(); // read incrementally
    $output .= $process->readError(); // read incrementally
}

// These will always return the full output
$output = $process->output();
$errorOutput = $process->error();
```

You can also pass an anonymous function to `waitUntil`, which allows you to wait until a certain
condition is met.

```php
$process = new BackgroundProcess(['backup','/var/www'])
$process->start();
$process->waitUntil(function ($output,$error) {
   return str_contains($output,'backup complete');
});
```