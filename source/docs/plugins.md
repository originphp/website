---
title: Plugins
description: Plugins Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Plugins 

You can also create Plugins which are a combinations of controllers,models,views etc and then you can use between apps by copying the plugin folder.

The first step to creating a plugin is to create the folders.

Lets say you want to create a contact manager plugin, you create the folder structure. It uses the same structure as the app folder, you can add what you need.

```
-- contact_manager
    |-- config
    |-- database
    |   -- migrations
    |-- src
    |   |-- Console
    |   |   |-- Command
    |   |-- Http
    |   |   |-- Controller
    |   |   |-- View
    |   |-- Model
    |-- tests
```

You can use the generate plugin to create the folder structure, routes and the plugin controller and model.

```linux
$ bin/console generate plugin ContactManager
```

Then in your `config/bootstrap.php` add:

`Plugin::load('ContactManager');` 

The plugin name in load should be camel case with the first letter capitalized e.g. ContactManager, but the folder should be `underscored`.

If you did not use the generate plugin then follow these steps.

## Setup Routing

Then you will need to setup the routing (if you are going to use). In your plugin folder create `config/routes.php` and add:

```php
<?php 
use Origin\Http\Router;
Router::add('/contact_manager/:controller/:action/*', ['plugin'=>'ContactManager']);
```

## Create ApplicationController

Create `ContactManagerApplicationController.php` in the `plugins/contact_manager/src/Controller` folder.

```php
<?php 
namespace ContactManager\Controller;

use app\Http\Controller\ApplicationController;

class ContactManagerApplicationController extends ApplicationController
{
}
```

## Create ApplicationModel

Create `ContactManagerApplicationModel.php` in the `plugins/contact_manager/src/Model` folder.

```php
<?php 
namespace ContactManager\Model;

use App\Model\ApplicationModel;

class ContactManagerApplicationModel extends ApplicationModel
{
}

```

## Loading models

From within the controller you use the loadModel method with plugin syntax. The loadModel both returns the model
and sets it up as property.

```php
$this->loadModel('ContactManger.Contact');
$results = $this->Contact->find('all');
```

## Installing Plugins

### Installing from remote repository (Public or private)

You can install plugins from remote GIT repositories using the `plugin:install` command, this will download the plugin into your plugins folder and append your application configuration to load the plugin.

To install a plugin from GitHub

```linux
$ bin/console plugin:install username/plugin.git 
```

To install a plugin from any repo

```
$ bin/console plugin:install https://github.com/username/plugin.git
```

To install the plugin in a custom plugin folder

```
$ bin/console plugin:install https://github.com/username/plugin.git NewPluginName
```

### Using Composer and packagist.org

If you want to package your plugin so it can be installed with Composer, simply add the plugin installer to your `composer.json`, set the packagist information and the namespace.

Lets say you created a new plugin called Super Cache, you would setup the `composer.json` like this

```json
{
    "name":"username/super-cache", // packagist info
    "description":"Super Cache Plugin",
    "type": "originphp-plugin",
    "autoload": {
      "psr-4": {
        "SuperCache\\": "src/",
        "SuperCache\\Test\\": "tests/"
      }
    },
    "require": {
        "originphp/plugin-installer": "*"
    }
  }
```

Then run composer require to add it to any of your projects.

```linux
$ composer require username/super-cache
```