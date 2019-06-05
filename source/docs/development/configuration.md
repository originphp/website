---
title: Configuration
description: Configuration Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Configuration

The configuration for your application can be found in the `config` folder. There you will find the configuration files for the database and the SMTP server settings for email.

## Database

Save a copy of `database.php.default` to `database.php` and update the database name,username and password.

## Email Accounts (SMTP)

Save a copy of `email.php.default` to `email.php` and update database name,username and password.

## Server

In the `config/environments` folder you will find a configuration file for each environment, e.g development or production. Keep your configuration for specific environments, if any here.

When setting up a new server simply copy the environment to the config folder e.g

```linux
$ cd <project folder>
$ cp config/environments/production.php config/server.php
```