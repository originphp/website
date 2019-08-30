---
title: Dockerized Development Environment
description: Dockerized Development Environment Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Dockerized Development Environment

OriginPHP comes with its own Dockerized Development Environment (DDE), this works like a real server, and will reduce the risk of issues when deploying your application to a server caused by different systems.

To work with Docker, install [Docker Desktop](https://www.docker.com/products/docker-desktop) then build the docker containers, this must be done from within the project folder

```linux
$ cd <folder>
$ docker-compose build
```

The container only needs to be built once, after this you will use the `up` and `down` commands to start and stop the docker container.

```linux
$ docker-compose up
```

Then open your web browser and go to [http://localhost:8000](http://localhost:8000)  which will show you a status page that all is working okay.

## Configuring the database

The Dockerized Development Environment (DDE) comes with MySQL server. To access the MySQL server from within the Docker container, we need to use its name which is `db` and not `localhost`.

```php
ConnectionManager::config('default', [
    'host' => 'db', // Docker MySQL container. Use localhost for built in webserver
    'database' => 'origin',
    'username' => 'root',
    'password' => 'root',
    'engine' => 'mysql'
]);
```

Even though the container is running, you will want to access the command line.

```linux
$ docker-compose run app bash
```

Then run the db console to set everything up for you.

```linux
$ bin/console db:setup
```

The db setup command will :

- Create the database
- Load the schema from `database/schema.sql` file (if its found)
- Seed the database with records from the `database/seed.sql` file if its found

If all went well when you go to [http://localhost:8000](http://localhost:8000)  it should now say that it is connected to the database.

### Access the database server manually

To access the MySQL client you will first need to access the container, then connect using the hostname `db` since the database is also run in a docker container.

```bash
$ docker-compose run app bash
$ mysql -h db -uroot -p
```

When it asks you for the password type in **root**, then copy and paste the following sql to create the database and grant permissions to a user.

> You can also access the MySql server using any database management application using `localhost` port `3306`. Mac users can use [Sequel Pro](https://www.sequelpro.com/) or Windows users can use [Heidi SQL](https://www.heidisql.com/).


### Configuring PostgreSQL in Docker

If you prefer to use PostgreSQL as your database then change the `DB_ENGINE` engine to `pgsql` in the `config/.env.php` file. You will also need to adjust the docker settings.

In the the root folder edit the `Dockerfile`, and add the following lines after `php-dev \` 
```
    postgresql-client \
    php-pgsql \
```

In the `docker-compose.yml` change the db section (or add underneath)

```yaml
db:
    image: postgres
    volumes:
      - pg-data:/var/lib/postgresql/data
    restart: always
    environment:
      POSTGRES_USER: root
      POSTGRES_PASSWORD: root
    ports:
        - "5432:5432"
```

and change the volume name

```yaml
volumes:
  pg-data:
```

Then rebuild the image

```linux
$ docker-compose build
```
