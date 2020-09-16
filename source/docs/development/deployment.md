---
title: Deployment
description: Deploying Your Apps
extends: _layouts.documentation
section: content
---
# Deployment

These instructions are for `Ubuntu 20.04`,`Apache` and `MySQL 8.x` setup.

## SSL Certificate

You can create a free SSL certificate with `letsencrypt` , to do, you need to install `certbot`

```bash
$ sudo apt install certbot python3-certbot-apache
```

Generate the SSL cert

```bash
$ sudo systemctl stop apache2
$ sudo certbot certonly --standalone -d app.example.com
```

At some point in the future you will need to renew the certificate, this can be done like this, you will need to stop the server first.

```bash
$ sudo systemctl stop apache2
$ sudo certbot renew
$ sudo systemctl start apache2
```

## Apache Virtual Host

Create the directory and set the permissions.

```bash
$ sudo mkdir /var/www/app.example.com
$ sudo chown -R www-data:www-data /var/www/app.example.com
$ sudo chmod -R 775 /var/www/app.example.com
```

Create the virtual host


```bash
$ sudo nano /etc/apache2/sites-available/app.example.com.conf
```

Add the following contents

```apache
<VirtualHost *:80> 
    ServerName app.example.com
    RewriteEngine on
    RewriteCond %{SERVER_NAME} =app.example.com
    RewriteRule ^ https://%{SERVER_NAME}%{REQUEST_URI} [END,NE,R=permanent]
</VirtualHost>
<VirtualHost *:443>
    ServerAdmin admin@example.com
    ServerName app.example.com
    DocumentRoot /var/www/app.example.com/public
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined

    <Directory /var/www/app.example.com/public>
        Options FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    SSLEngine on
    #   If both key and certificate are stored in the same file, only the
    #   SSLCertificateFile directive is needed.
    SSLCertificateFile  /etc/letsencrypt/live/app.example.com/cert.pem
    SSLCertificateKeyFile  /etc/letsencrypt/live/app.example.com/privkey.pem
</VirtualHost>
```


Enable the new site

```
$ sudo a2ensite app.example.com.conf
```

Check the configuration works

``` bash
$ sudo apachectl configtest
```

Reload

```bash
$ sudo systemctl start apache2
```

## Install Software from repo


```bash
$ cd /var/www/app.example.com
$ git clone https://github.com/username/repository.git .
```

Create cache folder and application log this is so we can set the permissions for it regardless who creates it (you/www-data)

```bash
$ mkdir tmp/cache/origin
$ touch logs/application.log
```

Set the permissions

```bash
$ sudo chown -R www-data:www-data /var/www/app.example.com
$ sudo chmod -R 775 /var/www/app.example.com
```

Install the composer dependencies

```bash
$ composer install
```

Create the .env file using the defaults

```bash
$ bin/install
```

## Configuration

Set the database, email and any other configuration settings

```bash
$ nano config/.env
```

## Create a MySQL User

```bash
$ sudo mysql
```

Create a user `application` and grant permissions to use the database also called `application`

```sql
CREATE USER 'application'@'%' IDENTIFIED WITH mysql_native_password BY 'secret!';
GRANT ALL ON application.* TO 'application'@'%';
FLUSH PRIVILEGES;
```

Run the DB setup

```
$ bin/console db:setup
```

## Updating from Git

When ever you want to deploy the latest version, just pull using `git`.

```
$ git pull
```

## Setting up Queues

```bash
$ sudo crontab -u www-data -e
```

Add the default queue and the mailers queue to `crontab`

```cron
*/5 * * * * cd /var/www/app.example.com && bin/console queue:worker
*/1 * * * * cd /var/www/app.example.com && bin/console queue:worker mailers
```

## Configure Apache to Cache Static Assets (CSS, JS etc)

Setup Apache to cache static assets

Enable the expires module

```bash
sudo a2enmod expires
```

Open your virtual host configuration file

```bash
$ nano /etc/apache2/sites-available/app.example.com.conf
```

Then add the following to your virtual host configuration 

```apache
<IfModule mod_expires.c>
	<FilesMatch "\.(jpe?g|png|gif|js|css)$">
		ExpiresActive On
		ExpiresDefault "modified plus 4 weeks"
    </FilesMatch>
</IfModule>
```

Next restart Apache

```bash
$ sudo service apache2 restart
```

## System Administration Fundamentals

At some point you will probably want to compress, uncompress, backup and restore and move files 
between servers or instances.

### Compressing and uncompressing

To compress

```bash
$ tar -czf websites.tar.gz /var/www
```

To uncompress

```bash
$ tar -zxvf websites.tar.gz
```

### MySQL Dump

To dump a database

```bash
$ mysqldump -u root -p database_name > dump.sql
```

To import a dumped database

```bash
$ mysql -u root -p database_name < dump.sql
```

### Copying a single file to a Remote Server

To copy a file remotely

```bash
$ scp dump.sql username@example.com:~
```

### Copying multiple files to a Remote Server

```bash
rsync -arv ~/code/www.example.com/ username@example:/var/www/www.example.com/public
```

## SSH Tunnel

Lets say you want to create a SSH tunnel for the MySQL port, you can then access the remote server
port `3306` on your `localhost` port `3307`.

```bash
$ ssh -L 3307:localhost:3306 user@example.com
```


## Generate a Random Password (Linux)

To generate a quick random password

```bash
$ openssl rand -hex 4
50ae7c0e
```