---
title: Multi Tenant Plugin
description: Multi Tenant Plugin Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Multi Tenant Plugin

The `MultiTenant` plugin enables you to make your web application multi-tenant using a single database.

## Installation

### Automated

Install the the `MultiTenant` plugin using the `plugin:install` command

```linux
$ bin/console plugin:install originphp/multi_tenant
```

> The plugin:install command requires that the git command line tools are installed

### Manually

To install the plugin manually, download the source into `plugins/multi_tenant`.

Add the following line to `config/bootstrap.php`

```php
Plugin::load('MultiTenant');
```

## Models

Extend any models using the `MultiTenantModel` and then add the column `tenant_id` to your database (and schema)

```php
use MultiTenant\MultiTenantModel;
class Contact extends MultiTenantModel
{

}
```

## Initializing MultiTenant

In your AppController initialize the Tenant after you have loaded the `AuthComponent`.

```php
use MultiTenant\Tenant;

class AppController extends Controller
{
    public function initialize()
    {
        $this->loadComponent('Auth');

        if ($this->Auth->isLoggedIn()) {
            $tenantId = $this->Auth->user('tenant_id');
            Tenant::initialize($tenantId);
        }
    }
}
```

By default the Tenant class will use the `tenant_id` as the foreign key, if you want to use a different foreign key, then set this in the options when initializing the Tenant, here is an example:

```php
Tenant::initialize($userId, [
    'foreignKey' => 'user_id'
]);
```

That is it, as long a tenant is initialized, any model finds will add the `tenant id`, and when you create a record the tenant id will be added automatically.