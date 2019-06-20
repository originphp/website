---
title: Getting Started
description: Console Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Getting Started with OriginPHP

## What is OriginPHP?

OriginPHP is a web application framework written in PHP that uses a number of well known software design patterns, including convention over configuration, MVC (Model View Controller), association data mapping, and front controller.

## Installation

Download and install [Composer](https://getcomposer.org/doc/00-intro.md), then run the following command to create a new project

```linux
$ composer create-project originphp/app <folder>
```

OriginPHP comes with both a built-in development server and a [dockerized development environment (dde)](/docs/development/dockerized-development-environment) which works exactly like a real server would work and includes MySQL. Its very easy to setup and work with and it will give you consistent results, see the [instructions](/docs/development/dockerized-development-environment) for more information.

To run the built-in development server:

```linux
$ bin/server 8000
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000) which will show you a status page that all is working okay.

### Configure the Database Connection

Open the file `config/database.php.default` in your IDE, I recommend [Visual Studio Code](https://code.visualstudio.com/). Set the host, database, username and password as follows and then save a copy as `database.php`.

```php
ConnectionManager::config('default', [
    'host' => 'localhost',
    'database' => 'origin',
    'username' => 'username',
    'password' => 'password',
    'engine' => 'mysql' // or pgsql
]);
```

Then run the db console to set everything up for you.

```linux
$ bin/console db:setup
```

The db setup command will :

- Create the database
- Load the schema from `db/schema.sql` file (if its found)
- Seed the database with records from the `db/seed.sql` file if its found

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.