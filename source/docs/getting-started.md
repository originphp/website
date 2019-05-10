---
title: Getting Started
description: Console Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Getting Started with OriginPHP

## What is OriginPHP?

OriginPHP is a web application framework written in PHP that uses a number of well known software design patterns, including convention over configuration, MVC (Model View Controller), association data mapping, and front controller.


## First time users

For the first time that you use OriginPHP, install the [bookmarks demo application](https://github.com/originphp/bookmarks) to get a feel for the features of OriginPHP. The instructions can be found in the Readme of the project.

## Installation

Download and install [Composer](https://getcomposer.org/doc/00-intro.md), then run the following command to create a new project

```linux
$ composer create-project originphp/app [application_name]
```

OriginPHP comes with a dockerized development environment.

Install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder

```linux
$ cd [application_name]
$ docker-compose build
```

The container only needs to be built once, after this you will use the `up` and `down` commands to start and stop the docker container.

```linux
$ docker-compose up
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000)  which will show you a status page that all is working okay.

### Configure the Database Connection

Open the file `config/database.php.default` in your IDE, I recommend [Visual Studio Code](https://code.visualstudio.com/). Set the host, database, username and password as follows and then save a copy as `database.php`.

```php
ConnectionManager::config('default', [
    'host' => 'db', // Docker MySQL container
    'database' => 'origin',
    'username' => 'root',
    'password' => 'root'
]);
```
> To access the MySQL server from within the Docker container, we need to use its name which is `db` and not `localhost`.

Even though the container is running, you will want to access the command line.


```linux
$ docker-compose run app bash
```

Then run the db console to set everything up for you.

```linux
$ bin/console db setup
```

The db setup command will :

- Create the database
- Load the schema from `config/db/schema.sql` file (if its found)
- Seed the database with records from the `config/db/seed.sql` file if its found

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.

### Access the database server manually

To access the MySQL client  you will first need to access the container, then connect using the hostname `db` since the database is also run in a docker container.

```bash
$ docker-compose run app bash
$ mysql -h db -uroot -p
```

When it asks you for the password type in **root**, then copy and paste the following sql to create the database and grant permissions to a user.

> You can also acces the MySql server using any database management application using `localhost` port `3306`. Windows users can use [Sequel Pro](https://www.sequelpro.com/) or Mac users can use [Heidi SQL](https://www.heidisql.com/).
