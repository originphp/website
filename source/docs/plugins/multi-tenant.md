---
title: MultiTenant Plugin
description: MultiTenant Plugin Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# MultiTenant Plugin

The `MultiTenant` plugin enables you to make your web application multi-tenant using a single database.

## Models

Add the `Tenantable` trait to any model and then add the column `tenant_id` to your database (and schema)

```php
use MultiTenant\Tenantable;

class Contact extends ApplicationModel
{
    use Tenantable;
}
```

## Initializing MultiTenant

In your AppController initialize the Tenant after you have loaded the `AuthComponent`.

```php
use MultiTenant\Tenant;

class AppController extends Controller
{
    public function initialize() : void
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

That is it, as long the `Tenant` instance has been initialized, any model that finds will add the `tenant id` to query, and when you create a record the tenant id will be added automatically.