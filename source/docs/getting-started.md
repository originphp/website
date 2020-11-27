---
title: Getting Started
description: Console Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Getting Started with OriginPHP

> This documentation is for version 3.x, see [2.x documentation](/2.0/getting-started) for the prior version. We also have produced an [upgrade guide](/docs/upgrade). Applications created on or after 17th July 2020 using `composer create-project` will be using the new version.

## What is OriginPHP?

OriginPHP is a web application framework written in PHP that uses a number of well known software design patterns, including convention over configuration, MVC (Model View Controller), association data mapping, and front controller.

## Installation

Download and install [Composer](https://getcomposer.org/doc/00-intro.md), then run the following command to create a new project

> If you run into any issues with Composer make sure you have the most recent version installed.

```linux
$ composer create-project originphp/app <folder>
```

OriginPHP comes with both a built-in development server and a [dockerized development environment (dde)](/docs/development/dockerized-development-environment) which works exactly like a real server would work and includes MySQL, Redis, Minio (Object Storage), Mailhog, and optionaly (PostgreSQL,Elasticsearch,Postwoman and Memcached). Its very easy to setup and work with and it will give you consistent results.

### Built-in Development server

To run the built-in development server:

```linux
$ cd <folder>
$ bin/server
```

> You can add the port number as an argument for example `bin/server 80`

Then open your web browser and go to [http://localhost:8000](http://localhost:8000) which will show you a status page that all is working okay.

### Dockerized Development Environment

To work with Docker, install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder. The build process takes a couple of minutes but only needs to be done once.

```linux
$ cd <folder>
$ docker-compose build
```

To start the [dockerized development environment](/docs/development/dockerized-development-environment), run the following command, and to stop the container hit `CTRL c`.

```linux
$ bin/docker up
```

> If you don't want to use the built in MySQL database, then use `docker-compose up` to start the docker container and `docker-compose down` to shut it down

Then open your web browser and go to [http://localhost:8000](http://localhost:8000) which will show you a status page that all is working okay.

The configuration settings for MySQL docker database are:

- host: **db** (from within the Docker container or localhost from outside)
- username: **root**
- password: **root**

To access the Docker container (linux prompt), use the `bash` script the same way you would use `docker-compose`.

```linux
$ bin/docker run app bash
```


> You can also access the MySql server using any database management application using `localhost` port `3307`. Mac users can use [Sequel Pro](https://www.sequelpro.com/) or Windows users can use [Heidi SQL](https://www.heidisql.com/).

If you want to work with PostgreSQL then see the [dockerized development environment (dde)](/docs/development/dockerized-development-environment) guide for information on how to configure the docker container to use this instead.

### Configure the Database Connection

When you create a new project with Composer it will run the `App\Console\InstallCommand`, which will create a copy of `config/.env.default` and save as `config/.env` file, this contains the environment vars for this installation, if you are not using Dockerized Development Environment then you will need to adjust the database settings.


You can find the database settings in `config/.env`, to use with Docker change `localhost` to `db`

```php
DB_HOST=localhost  # db for docker or localhost
DB_USERNAME=root
DB_PASSWORD=root
```

Next you need to run the `db:setup` command, if you are using the Dockerized Development Environment, you will need to access the container first, this is because the hostname for accessing the MySQL server is different from within the container.

To access the Docker container, use the `bash` script the same way you would use `docker-compose`.

```linux
$ bin/docker run app bash
```

Then run the `db:setup` command to set everything up for you.

```linux
$ bin/console db:setup
```

The db setup command will :

- Create the database
- Load the schema from `database/schema.php` file (if its found)
- Seed the database with records from the `database/seed.php` file if its found

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.