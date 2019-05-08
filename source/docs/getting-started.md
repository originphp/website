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

### Create the Database

Lets create the database on the server, from the command line type in the following to access the container and then the MySQL server.

```bash
$ docker-compose run app bash
$ mysql -h db -uroot -p
```

To access the MySQL server from within the docker the container, you need to use the host name `db`.

When it asks you for the password type in **root**, then copy and paste the following sql to create the database and grant permissions to a user.

```sql
CREATE DATABASE origin CHARACTER SET utf8mb4;
GRANT ALL ON origin.* TO 'origin' IDENTIFIED BY 'secret';
FLUSH PRIVILEGES;
QUIT
```

> You can also acces the MySql server using any database management application using `localhost` port `3306`. Windows users can use [Sequel Pro](https://www.sequelpro.com/) or Mac users can use [Heidi SQL](https://www.heidisql.com/).

### Configure the Database

Open the `database.php.default` in your IDE, I recommend [Visual Studio Code](https://code.visualstudio.com/). Set the host, database, username and password as follows and then save a copy as `database.php`.

```php
ConnectionManager::config('default', [
    'host' => 'db', // Docker MySQL container
    'database' => 'origin',
    'username' => 'origin',
    'password' => 'secret'
]);
```

> To access the MySQL server from within the Docker container, we need to use its name which is `db` and not `localhost`.

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.