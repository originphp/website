---
title: Code Generation
description: Code Generation Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Code Generation

OriginPHP comes with the generate command to save time using boilerplate templates for creating your class files.

To use generate in an interactive way

```linux 
$ bin/console generate
```

When using generate it expects the name of the class to be studly caps LemonPie and it also accepts plugin syntax. MyPlugin.LemonPie, which would then generate the files in a plugin's folder.

For example to generate a Posts controller in the src folder

```linux 
$ bin/console generate controller Posts
```
To generate the Posts controller in the Cms plugin

```linux 
$ bin/console generate controller Cms.Posts
```

## Command

To generate a console `Command`:

```linux 
$ bin/console generate command CacheReset
```

## Concern (Controller)

To generate a `Concern` for a `Controller`

```linux 
$ bin/console generate concern_controller ResponseChecker
```

## Concern (Model)

To generate a `Concern` for a `Model`

```linux 
$ bin/console generate concern_model SoftDeleteable
```

## Command

To generate a `Command`

```linux 
$ bin/console generate command BackupDatabase
```

## Component

To generate a `Component` to be used by your `Controllers`.

```linux 
$ bin/console generate component FormSecurity
```

## Controller

When you create a `Controller`, it will also create the integration test file.

```linux 
$ bin/console generate controller Posts
```

You also add a list of methods separated by spaces afterwards, then the methods will be added to the controller and test file, and views for those methods will also be created.

```linux 
$ bin/console generate controller Posts index add edit
```

## Entity

To generate an `Entity` class

```linux 
$ bin/console generate entity User
```

## Exception

To generate an `Exception` class

```linux 
$ bin/console generate exception NotFound
```

## Helper

To generate a `Helper` to be used in your views.

```linux 
$ bin/console generate helper FunkyForm
```

## Job

To generate a `Job` class

```linux 
$ bin/console generate job ClearSessions
```

## Listener

To generate a `Listener` class

```linux 
$ bin/console generate listener OrderNotifier
```

## Mailbox

To generate a `Mailbox` class

```linux 
$ bin/console generate mailbox Support
```

## Mailer

To generate a `Mailer` class

```linux 
$ bin/console generate mailer WelcomeEmail
```

## Model

To generate a `Model`, its test and fixture file.

```linux 
$ bin/console generate model User
```

If you do not have the database then you also add the create table schema like this

```linux 
$ bin/console generate model User name:string description:text age:integer active:boolean credits:decimal since:date created:datetime
```

This will also create a migration file to create the table for you. Once you have done this run the `db:migrate` command to create the table.

```linux 
$ bin/console db:migrate
```

## Middleware

To create a `Middleware` class

```linux 
$ bin/console generate middleware FormSecurity
```

## Migration

To quickly create a `Migration`

```linux 
$ bin/console generate migration AddProductTableIndex
```

## Plugin

If you are going to make a new `Plugin`, then the generate tool will create all the directories and base files for you.

```linux 
$ bin/console generate plugin ContactManager
```

Then run the other generators to create the code in the plugin folder.

```linux 
$ bin/console generate controller ContactManager.Contacts
$ bin/console generate model ContactManager.Contact
```

## Query

To generate a `Query` object class

```linux 
$ bin/console generate query BooleanSearch
```

## Repository

To generate a `Repository`

```linux 
$ bin/console generate repository Users
```

## Services (Service Objects)

To generate a `Service` (Service Object) class

```linux 
$ bin/console generate service SlackNotification
```

## Scaffold

If you have your database setup with tables, and you need to build a base for crud application, scaffold is what you want. It will generate a working prototype based using your tables.

```linux 
$ bin/console generate scaffold User
```

Just type in the name of the `model`, so User will look for the `users` table.