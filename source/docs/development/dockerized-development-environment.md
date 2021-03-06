---
title: Dockerized Development Environment
description: Dockerized Development Environment Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---

# Dockerized Development Environment

> As of March 6th 2021, `bin/docker` command has been removed from new application template. Everything is done using only the `Dockerfile` and `docker-compose.yml` file.

OriginPHP comes with its own Dockerized Development Environment (DDE), this works like a real server, and will reduce the risk of issues when deploying your application to a server caused by different systems.

The Dockerized Development Environment is configured out the box to work with PHP 7.4 and Apache, MySQL and Redis.

## Getting started with Docker

To work with Docker, install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder

```bash
$ cd <folder>
$ docker-compose build
```

The container only needs to be built once, after this you will use the `up` and `down` commands to start and stop the docker container.

To start the container (Apache + PHP)

```bash
$ docker-compose up
```

To stop container

```bash
$ docker-compose down
```

Then open your web browser and go to [https://localhost](https://localhost) which will show you a status page that all is working okay.

## Customizing Docker

Simply comment out the services that you do not want or uncomment the services that you want to use in the `docker-compose.yml` file.

Some services such as MySQL, Postgres, Minio and Elasticsearch rely on volumes, so remember to uncomment out the relevant volumes at the bottom of the configuration file when enabling the servies.

## Elasticsearch

Elasticsearch is not enabled by default, you will need to uncomment out the service configuration and volume in the `docker-compose.yml` file.

To access the elasticsearch server within docker, use the host name `elasticsearch`.

## Mailhog

Mailhog allows you to catch outgoing emails without actually sending them. Mailhog is not enabled by default, you will need to uncomment out the service configuration and volume in the `docker-compose.yml` file.

The web based interface can be accessed at `http://localhost:8025/`

Mailhog is configured by default in the `config/.env`, but it looks like this

```bash
EMAIL_HOST=mailhog # docker service name
EMAIL_PORT=1025
EMAIL_USERNAME=null
EMAIL_PASSWORD=null
EMAIL_SSL=false
EMAIL_TLS=false
```

## Memcached

Memcached is not enabled by default, you will need to uncomment out the service configuration in the `docker-compose.yml` file.

To access the memcached server within docker, use the host name `memached`.

## Minio (S3)

[Minio](https://min.io/) is a S3 compatible object storage, which you can use with [Storage](/docs/storage).

Minio is not enabled by default, you will need to uncomment out the service configuration in the `docker-compose.yml` file. you will need to uncomment out the service configuration in the `docker-compose.yml` file.

To access via the web browser, you can go to `http://localhost:9000` using the credentials that are set in the `docker-compose.yml` file.

Minio environment configuration is set in `config/.env`.

```bash
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

To access the MySQL server from within the docker container use the host name `db` and port `3306`, and to access locally using any database management application use `localhost` port `3306`.

You can find the database settings in `config/.env`, it works out of the box when using docker.

```php
DB_HOST=db  # db for docker or localhost
DB_USERNAME=root
DB_PASSWORD=root
```

To access from the command line, first go into the docker container, use the `bash` script the same way you would use `docker-compose`.

```bash
$ docker-compose exec app bash
```

Then type the following command to access the MySQL client, when prompted for password, use `root`.

```bash
mysql -h db -uroot -p
```

## PostgreSQL

[PostgreSQL](https://www.postgresql.org/) is an open source relational database. PostgreSQL is not enabled by default, you will need to uncomment out the service and volume configuration in the `docker-compose.yml` file.

You will also need to change the engine class in `config/database.php`

To access the PostgreSQL server from within the docker container use the host name `db` and port `5432`, and to access locally using any database management application use `localhost` port `5432`.

You can set the database settings in `config/.env`.

```php
DB_HOST=db  # db for docker or localhost
DB_USERNAME=root
DB_PASSWORD=root
```

To access from the command line, first go into the docker container

```bash
$ docker-compose exec app bash
```

Then type the following command to access the PostgreSQL frontend, when prompted for password, use `root`.

```bash
psql -U root -d application -h db
```

## Hoppscotch (previously called Postwoman)

[Postwoman](https://github.com/hoppscotch/hoppscotch) is fast, free open source alternative to `postman`, it used to create and test requests to your app. Hoppscotch is not enabled by default, you will need to uncomment out the service and volume configuration in the `docker-compose.yml` file.

To access Hoppscotch goto `http://localhost:3000/`

## Redis

Redis can be used for both caching and queues, this is enabled by default.

In your cache or queue settings, set the host to `redis`.
