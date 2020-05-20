---
title: Dockerized Development Environment
description: Dockerized Development Environment Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Dockerized Development Environment

OriginPHP comes with its own Dockerized Development Environment (DDE), this works like a real server, and will reduce the risk of issues when deploying your application to a server caused by different systems.

> The DDE for projects created prior to May 20th 2020 only include Apache, PHP and MySQL.

The Dockerized Development Environment is configured out the box to work with PHP 7.4 and Apache and you can also easily configure extra services `mysql`, `postgresql`,`redis`,`memcached`,`elasticsearch`,`minio` and `mailhog`

## Getting started with Docker

To work with Docker, install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder

```linux
$ cd <folder>
$ docker-compose build
```

The container only needs to be built once, after this you will use the `up` and `down` commands to start and stop the docker container.

To start the container (Apache + PHP)

```linux
$ docker-compose up
```

To stop container

```linux
$ docker-compose down
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000)  which will show you a status page that all is working okay.

## Using Docker with extra services

The dockerized development container can be run with additional services such `mysql` or `postgresql`,`redis`,`memcached`,`elasticsearch`,`minio` and `mailhog`

To fire up the docker container with the extra services (`mysql`,`redis`,`minio`,`mailhog`) run the following command

```linux
$ bin/docker
```

> When running this command, server debug information is displayed on the screen, and might display necessary login, access  and troubleshooting information.

To stop the container from the same window hit `CTRL C`, if you don't, then it will leave orphans, and you will have to run `docker-compose down --remove-orphans` manually.

In the `docker` folder, you can find additional services for docker and you can customize which services are being run
by editing the `bin/docker` script.


## Elasticsearch

Elasticsearch is not enabled by default, to enable this edit the `bin/docker` script, adding  `:docker/elasticsearch.yml` to the `export` line.

If `bin/docker` is already started, then hit `CTRL C` first, then run

```
$ bin/docker
```

To access the elasticsearch server within docker, use the host name `elasticsearch`.

## Mailhog

Mailhog allows you to catch outgoing emails without actually sending them.

The web based interface can be accessed at `http://localhost:8025/`

Mailhog is configured by default in the `config/.env`, but it looks like this

```linux
EMAIL_HOST=mailhog # docker service name
EMAIL_PORT=1025
EMAIL_USERNAME=null
EMAIL_PASSWORD=null
EMAIL_SSL=false
EMAIL_TLS=false
```

## Memcached

Memcached is not enabled by default, to enable this edit the `bin/docker` script, adding  `:docker/memcached.yml` to the `export` line.

If `bin/docker` is already started, then hit `CTRL C` first, then run

```
$ bin/docker
```

To access the memcached server within docker, use the host name `memached`.

## Minio (S3)

[Minio](https://min.io/) is a S3 compatible object storage, which you can use with [Storage](/docs/storage). To access
via the web browser, you can go to `http://localhost:9000` using the credentials that are set in `docker/minio.yml`.

Minio is configured by default in the `config/.env`, but it looks like this

```linux
S3_KEY=minio
S3_SECRET=b1816172fd2ba98f3af520ef572e3a47
S3_ENDPOINT=http://127.0.0.1:9000
S3_BUCKET=development
```

Then configure `config/storage.php` so it looks like this if you want to use it as your default storage engine.

```php
use Origin\Storage\Engine\S3Engine;

return [
  'default' => [
    'className' => S3Engine::class
    'credentials' => [
        'key' => env('S3_KEY'), // * required
        'secret' => env('S3_SECRET'), // * required
    ],
    'region' => 'us-east-1', // * required
    'version' => 'latest',
    'endpoint' => env('S3_ENDPOINT'), // for S3 comptabile protocols
    'bucket' => env('S3_BUCKET') // * required
  ]
];
```

### MySQL

[MySQL](https://www.mysql.com/) is an open source relational database. 

To access the MySQL server from within the docker container use the host name `db` and port `3306`, and to access locally using any database management application use `localhost` port `3307`.

You can find the database settings in `config/.env`, it works out of the box when using docker.

```php
DB_HOST=db  # db for docker or localhost
DB_USERNAME=root
DB_PASSWORD=root
```

To access from the command line, first go into the docker container

```linux
$ docker-compose run app bash
```

Then type the following command to access the MySQL client, when prompted for password, use `root`.

```linux
mysql -h db -uroot -p
```

## PostgreSQL

[PostgreSQL](https://www.postgresql.org/) is an open source relational database. PostgreSQL is not enabled by default, to enable this edit the `bin/docker` script, replace the `:docker/mysql.yml` with  `:docker/postgresql.yml` in the `export` line.

> Remember to change the engine class in `config/database.php`

To access the PostgreSQL server from within the docker container use the host name `db` and port `5432`, and to access locally using any database management application use `localhost` port `54320`.

You can find the database settings in `config/.env`, it works out of the box when using docker.

```php
DB_HOST=db  # db for docker or localhost
DB_USERNAME=root
DB_PASSWORD=root
```

To access from the command line, first go into the docker container

```linux
$ docker-compose run app bash
```

Then type the following command to access the PostgreSQL frontend, when prompted for password, use `root`.

```linux
psql -U root -d application -h db
```

## Postwoman

[Postwoman](https://github.com/liyasthomas/postwoman) is fast, free open source alternative to `postman`, it used to create and test requests to your app. Postwoman is not enabled by default, to enable this edit the `bin/docker` script, adding  `:docker/postman.yml` to the `export` line.

If `bin/docker` is already started, then hit `CTRL C` first, then run

```
$ bin/docker
```

To access postwoman goto `http://localhost:3000/`

## Redis

Redis can be used for both caching and queues, this is run by default when using `bin/docker`.

In your cache or queue settings, set the host to `redis`.