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

OriginPHP comes with both a built-in development server and a [dockerized development environment (dde)](/docs/development/dockerized-development-environment) which works exactly like a real server would work and includes MySQL. Its very easy to setup and work with and it will give you consistent results.

### Built-in Development server

To run the built-in development server:

```linux
$ cd <folder>
$ bin/server 8000
```
Then open your web browser and go to [http://localhost:8000](http://localhost:8000) which will show you a status page that all is working okay.

### Dockerized Development Environment

To work with Docker, install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder. The build process takes a couple of minutes but only needs to be done once.

```linux
$ cd <folder>
$ docker-compose build
```

Once the Docker container has been built, you will use the `up` and `down` commands to start and stop the docker container which takes seconds.

```linux
$ docker-compose up
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000) which will show you a status page that all is working okay.

The configuration settings for MySQL are:

- host: db (from within the Docker container)
- username: root
- password: root

To access the Docker container (linux prompt).

```linux
$ docker-compose run app bash
```

> You can also access the MySql server using any database management application using `localhost` port `3306`. Mac users can use [Sequel Pro](https://www.sequelpro.com/) or Windows users can use [Heidi SQL](https://www.heidisql.com/).

If you want to work with PostgreSQL then see the [dockerized development environment (dde)](/docs/development/dockerized-development-environment) guide for information on how to set this up.

### Configure the Database Connection

Open the file `config/database.php.default` in your IDE, I recommend [Visual Studio Code](https://code.visualstudio.com/). Set the host, database, username and password as follows and then save a copy as `database.php`.

```php
ConnectionManager::config('default', [
    'host' => 'db',
    'database' => 'origin',
    'username' => 'root',
    'password' => 'root',
    'engine' => 'mysql' // or pgsql
]);
```

Next you need to run the `db:setup` command, if you are using the Dockerized Development Environment, you will need to access the container first, this is because the hostname for accessing the MySQL server is different from within the container.

To access the Docker container:

```linux
$ docker-compose run app bash
```

Then run the `db:setup` command to set everything up for you.

```linux
$ bin/console db:setup
```

The db setup command will :

- Create the database
- Load the schema from `db/schema.sql` file (if its found)
- Seed the database with records from the `db/seed.sql` file if its found

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.