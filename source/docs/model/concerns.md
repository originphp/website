---
title: Concerns - Model
description:  Concerns - Model Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Concerns

Concerns are `traits` which you use to share code between models or change the behavior of the `Model` that it is attached too. Concerns for models go in the `app/Model/Concern` folder.

The first thing to do is generate some code

```linux
$ bin/console generate concern_model Sluggable
```

This will create two files

```
[ OK ] /var/www/app/Model/Concern/Sluggable.php
[ OK ] /var/www/tests/TestCase/Model/Concern/SluggableTest.php
```

Then we will adjust this to register a callback for `beforeSave`

```php
namespace App\Model\Concern;

use Origin\Model\Entity;
use Origin\Utility\Text;
use ArrayObject;

trait Sluggable
{
    /**
     * Initialization method, here you can register callbacks or configure model
     * associations
     *
     * @return void
     */
    protected function initializeSluggable() : void
    {
        $this->beforeSave('slugTitle');
    }

    /**
     * Is a registered callback
     *
     * @param \Origin\Model\Entity $record
     * @param ArrayObject $options
     * @return boolean|void
     */
    protected function slugTitle(Entity $record, ArrayObject $options)
    {
        if ($record->title) {
            $record->slug = Text::slug($record->title);
        }
    }
}
```

Then add the `Concern` to a `Model`.

```php
use App\Model\Concern\Sluggable;
class Article extends ApplicationModel
{
  use Sluggable;
}
```

### Initializer Trait

Models have the `InitializerTrait` enabled, enables you to use an initialization method on traits. See the [InitializerTrait guide](/docs/initializer-trait) for more information.


## Registering Callbacks

As the `Concern` is attached to the model, you can register [callbacks](/docs/model/callbacks) from within the `Concern`.

## Disabling callbacks

To disable a callback from within the `Model`:

```php
$this->disableCallback('slugTitle');
```

Then to re-enable:

```php
$this->enableCallback('slugTitle');
```

## Model Concerns

OriginPHP comes with a few `Concerns` 

### Timestampable

This `Concern` updates created and updated fields when creating or saving records, this is also enabled by default in the `ApplicationModel`.

### Delocalizable

This `Concern` delocalizes data. This is enabled by default in the `ApplicationModel`, and during the save process the data is parsed into MySQL format.

### Elasticsearch

Automatically integrate your application with Elasticsearch. See the [Elasticsearch Concern](/docs/model/elasticsearch-concern) for more information.