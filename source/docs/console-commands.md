---
title: Console Commands and Applications
description: Console Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Console Commands

To see the available list of commands run the console script without any arguments. This will show the built-in commands and any commands that you have created.

```linux
$ bin/console
```

## Creating Commands

To create a `Command`, use the code generator.

```linux
$ bin/console generate command Backup
```

This will create the `BackupCommand` file  in the `/src/Command` folder and the `BackupCommandTest` file in the `/tests/TestCase/Command/` folder.

Here is what a simple Command looks like

```php
namespace App\Command;
use Origin\Command\Command;

class HelloCommand extends Command
{
    protected $name = 'hello';
    protected $description = 'A simple hello world command.';

    public function execute(){
       $this->out('Hello world');
    }
}
```

To run the command

```linux
$ bin/console hello
```

When you run the Command the initialize function is called first, then the `execute` method is run.

## Command Conventions

The class name of the `Command`, should be in camel case and it needs to end with Command. For example, BackupCommand and SuperBackupCommand.

The actual command names that use to interact with from the console, are in lowercase and may contain letters, hypens (-) and colons (:). These are set using the `name` property.

Use colons to add commands to their own namespace for grouping. For example `database:backup` and `database:restore`. The class names for this will be DatabaseBackupCommand and DatabaseRestoreCommand.

```php
class DatabaseBackupCommand extends Command
{
    protected $name = 'database:backup';
}
```

To run the command

```linux
$ bin/console database:backup
```

## Parsing Command Line Arguments

Arguments called after the command are either just an argument or an option.

### Options

When using the command line (cli) there are two types of options long and short. A long option looks like `--datsource` and a short option looks like `-ds`, basically with just one minus sign instead of two. If you want to pass a value to the option then it would be written like this `--datasource=mydb`.

By default when adding an option the datatype is considered a string e.g `--datasource=mydb` and boolean is `--dry-run` which if this option is passed will return true.

To add an option to your command, add the code to the `initialize` method.

```php

    public function initialize(){
        $this->addOption('datasource',[
            'description' => 'The datasource to use',
            'short' => 'ds',
            'default' = 'main'
        ]);
         $this->addOption('type',[
            'description' => 'The type of file',
            'required' => true
        ]);
    }

    public function execute(){
        $datasource = $this->options('datasource');
        $fileType = $this->options('type');
        ...
    }
```

This will allow you to parse these options as follows

```linux
$ bin/console backup --ds=test --type=zip
```

When adding an option, you can pass an array with the following keys

- description: this what will be displayed in the help (ie. --help)
- short: this the short name for the option (or an alias)
- required: default is fale, but if you set this to true then it will trigger an error if the value is not provided.
- default: this is the default value to return unless the option is passed with something else
- type: default is string. Can be string, boolean, numeric.

### Arguments

Arguments are different that options, since they are obtained by the order that they are provided.

```php
    public function initialize(){
        $this->addArgument('database',[
            'description' => 'The name of the database',
            'required' => true
        ]);
        $this->addArgument('tables',[
            'description' => 'The name of the tables to backup seperated by a space',
            'type' => 'array'
        ]);
    }

    public function execute(){
        $database = $this->arguments('database');
        $tables =  $this->arguments('tables');
        ...
    }
```

So now you can accept arguments to backup the tables products and users from the my_database. 

```linux
$ bin/console backup my_database products users
```

When adding an argument, you can pass an array with the following keys

- description: this what will be displayed in the help (ie. --help)
- required: default is false, but if you set this to true then it will trigger an error if the value is not provided.
- type: default is string. Can be string, boolean, numeric, array and hash (e.g key:value)


### Output

The Command uses the ConsoleIO object to create output to create some pretty straight forward output.

```php
$this->out('Some text');
```

These are wrappers and will wrap text in the same name tags and color this according.

```php
$this->success('Something went well'); // stdout
$this->info('This is some information'); // stdout

$this->warning('This some warning text'); // stderr
$this->error('This is an error'); // stderr


```

When you send messages to the debug method, these will only be displayed if the `--verbose` option is called. This used to display detailed or debug information to the user.

```php
$this->debug('This some warning text');
```

## Loading Models

Call `loadModel` from within your console command to load any `Model`, this works just like in `Controllers`.

```php
    public function initialize(){
        $this->loadModel('User');
    }

    public function execute(){
        $users = $this->User->find('list');
    }
```

## Running Other Commands

If you need to run another command from within your command, use the same command name that you would use
from the console and then pass an array of arguments

```php
function execute(){
    $this->runCommand('database:backup',[
        '--datasource' => $datasource, # option with value
        'my-database-name', # argument
        '--filename=monthly-backup.zip', # also option with value
        '--verbose' # a boolean option
    ]);
}
```

## Error Handling and Exiting the Command

There are various ways to throw errors and exit the command, the most important thing is that it is done in a friendly way so the integration testing can work.

### Throwing an Error

To display and error message and exit the `Command` you can use the following from within the command.

```php
$this->throwError('OMG something has gone wrong','The devil is in the details');
```

What this is does, is first it renders the error message and then it runs the `abort` method.

### Abort

This will exit the command and the integration testing will pick this up as an error.

```php
$this->abort();
```

### Exit

This will exit the command. You don't have to call this, returning out of the method is usually enough.

```php
$this->exit();
```

Sometimes you might want to display a different type of error message

```php
$this->io->block('OMG Something has gone wrong',['background'=>'red','color'=>'white'])
$this->abort();
```

## ConsoleIO Object

The ConsoleIO object handles the input and output from within Commands.

### Output

```php
$io->out('Text written to stdout');
$io->err('Text written to stderr');
```

The IO object also has some predefined styles of its own for the following.

```php
$io->success('Something went well'); // stdout
$io->info('This is some information'); // stdout

$io->warning('This some warning text'); // stderr
$io->error('This is an error'); // stderr

```

This is what a warning looks like when using the IO warning

![Console Block](/assets/images/console-warning.png)


### Asking Questions

```php
public function execute(){
    $name = $this->io->ask('What is your name?');
}
```

### Asking Questions with Choices

You can ask multiple choice questions like this:

```php
public function execute(){
    $response = $this->io->askChoice('Are you sure',['yes','no'],'no'); // default no
    if($response === 'no'){
        $this->abort();
    }
}
```

### Styles

#### Title

```php
$this->io->title('My Title');
```

This will output

```
My Title
========
```

#### Heading

```php
$this->io->heading('Some Heading');
```

This will output

```
Some Heading
------------
```

#### Text

When working with heading and titles, you can use text, which indents the text for you.


```php
$this->io->heading('Some Heading');
$this->io->text('The quick brown fox..');
```

This will output

```
Some Heading
------------
  The quick brown fox..
```

#### Lists

To output a list or list item. You can also pass an array with options.


```php
$this->io->list(['download file'])
$this->io->list('unpack file','-')
```

This will output

```
  * download file
  - unpack file
```

### Styling Text

A number of default styles are provided, however you can create your own. To get the list of styles.

```php
$styles = $io->styles();
debug($styles);
```

To create a custom style

```php
$this->io->styles('fire',['background'=>'lightRed','color'=>'white','blink'=>true]);
```

To use this style just wrap text in fire tags.

```php
$this->out('<fire>I am on fire</fire>');
```

#### Table

To draw a table with headers

```php
$this->io->table([
    ['heading 1','heading 2','heading 3'],
    ['text a','text b','text c'],
    ['text d','text e','text f'],
    ['text g','text h','text i'],
]);
```

Which will output this, if you dont want headers then just pass a second argument of false.

```
+------------+------------+------------+
| heading 1  | heading 2  | heading 3  |
+------------+------------+------------+
| text a     | text b     | text c     |
| text d     | text e     | text f     |
| text g     | text h     | text i     |
+------------+------------+------------+
```

### Progress Bar

Progress bars are also simple

```php
    for($i=0;$i<11;$i++){
        $this->io->progressBar($i,10);
        sleep(1);
    }
```

This will look something like this

![Console Progress Bar](/assets/images/console-progress-bar.png)

### Blocks

Want to disply text in a graphical block

```php
$this->io->block('This is a funky block',['background'=>'yellow','color'=>'white']);
```

![Console Block](/assets/images/console-block.png)

### Alerts

To display an alert

```php
  $this->io->alert('This is a alert box.',['background'=>'lightRed','color'=>'white']);
```

![Console Alert](/assets/images/console-alert.png)


### Creating Files

A common theme in console commands is creating files, and if the file exists, then ask the user if the command should continue. All of this is wrapped up in the create file method, if the user cancels or the file could not be saved then it will return `false`.

```php
$result = $this->io->createFile($filename,$contents);
```

## Packing Your Commands into a Console Application

Create a PHP file, making sure the path to the bootstrap.php file is correct. If you only add one command, then 
it is treated as single command application and it will automatically run the command when you run the application. 

```php
#!/usr/bin/env php
require __DIR__ . '/vendor/originphp/framework/src/bootstrap.php';
use Origin\Console\ConsoleApplication;
 
$app = new ConsoleApplication();
$app->name('database');
$app->description([
 'DB application for backing up and restoring the database'
])
$app->addCommand('backup', 'DatabaseBackup'); // adds DatabaseBackupCommand
$app->addCommand('restore', 'DatabaseRestore'); // adds DatabaseBackupCommand
$app->run();
```

## Running Commands as Cron Jobs

Many applications will need to run cron jobs, these can be to clean the database, send out emails, carry out tasks etc. You can run your `Commands` through cron by editing the cron file.

On Ubunu or other Debian based flavors of unix use the crontab command.

```linux
    sudo crontab -u www-data -e
```

For Redhat or Redhat base distributions edit the `/etc/crontab` file, although at the time of writing Redhat does not officially support Php 7.0.

To setup a cron job to run the `app:send-emails` command once each day

```
0 1 * * * cd /var/www/project && bin/console app:send-emails
```

For help with cron schedule expressions, see [cron guru](https://crontab.guru).


## Testing Commands

For information on how to test commands, see [testing](/docs/development/testing).