---
title: Events
description: Events Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Events

The Events module follows the Publisher-Subscriber pattern and can help you decouple your code in to a more hexagonal style architecture.

## How it works

You can this feature to any object, first include the `PublisherTrait`, then you need to subscribe listeners (more on this an a moment);

```php
use Origin\Publisher\PublisherTrait;
class Order extends Model
{
    use PublisherTrait; # 1
    
    public function initialize(array $config) : void
    {
        $this->subscribe('OrderNotifier'); #2
    }

    public function afterSave(Entity $entity, ArrayObject $options) : void
    {
        if($created)
        {
            $this->publish('orderCreated',$entity); #3
        }
    }
}
```

When this model is saved and a new order is created, it will publish the event to the all subscribed listeners on this object and the global listeners.

## Listeners

Lets create a listener, this will create the file  `app/Listener/OrderNotifierListener.php`.

```php
$ bin/console generate listener OrderNotifier
```

Add the `orderCreated` method

```php
use Origin\Publisher\Listener;
use Origin\Model\Entity;

class OrderNotifierListener extends Listener
{
    public function intitialize()
    {
        $this->loadModel('User');
    }

    public function orderCreated(Entity $order)
    {
        # put your logic here
    }
}
```

If you need to stop the process, in your listener return false, then any subsequent listeners will also be stopped.

## Subscribing

You can subscribe as many Listeners as you need

```php
public function initialize(array $config) : void
{
    $this->subscribe('OrderNotifier');
    $this->subscribe(CustomerAnalyticsListener::class);
}
```

If the object is already created, example `$this` then you can pass the object as well.

```php
public function initialize(array $config) : void
{
    $this->subscribe($this);
    $this->subscribe(new LoggingListener());
}
```

If you want to subscribe to selected events

```php
public function initialize(array $config) : void
{
    $this->subscribe('OrderNotifier',[
        'on' => ['orderCancelled','orderCreated']
    ]);
}
```

## Publishing

When you publish an event, you can use any number of arguments. The event name is method name
that will be called on the Listener.

```php
$this->publish('orderCreated',$arg1,$arg2,$arg3);
```

## Background Processing

If you have have configured [Queues](/docs/queue) you can queue the Listener to be run in the background, it is added to the `listeners` queue using the `default` queue connection.

```php
public function initialize(array $config) : void
{
    $this->subscribe('OrderNotifier',['queue'=>true]);
}
```

## Global

To create events globally,create a file the `config/listeners.php` like this

```php
use Origin\Publisher\Publisher
$publisher = Publisher::instance();

# here you can subscribe all the listeners
$publisher->subscribe(new AnalyticsListener());
```

Configure `config/bootstrap.php`, load this file

```php
include 'application.php';
...
include 'listeners.php`;
```