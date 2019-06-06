---
title: Date Helper
description: Date Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Date Helper

The date helper makes it easy to format dates, in your `AppController` setup the default timezone, and date formats (using PHP date function style formats), and the date helper will automatically format dates and times unless you tell it to use a different format. The date helper uses the Date utility.

```php
use Origin\Utility\Date;

public function initialize(){
     Date::locale([
         'timezone' => 'UTC',
         'date' => 'm/d/Y',
         'datetime' => 'm/d/Y H:i',
         'time' => 'H:i'
         ]);
}
```

> If you are using [internationalization](/docs/development/internationalization-i18n) then the Date utility will be configured automatically when you call **I18n::initialize()**.

## Formating

From within your view you would use like this, it will automatically format the date/time/datetime depending upon the field type. The date helper assumes that the date in the database is stored in MySQL date/time formats. If you set the timezone to anything other than UTC, then the date utility will automatically convert times etc. Setting the timezone does not change the PHP script timezone, it is only used by the date utility.

```php
echo $this->Date->format($article->created); // From 2019-01-01 13:45:00 to 01/01/2019 13:45
```

If you want to format it a different way, you can do so. Time values will still be converted to local time if you set the timezone to anything other than UTC.

```php
echo $this->Date->format($article->created,'F jS Y'); // January 1st 2019
```

## Parsing

The date formatter assumes that the dates that you are formatting are in MySQL format, e.g. Y-m-d H:i:s. You can use the Date utility to delocalize user submitted data to convert to this format and timezone.

This can be done in middleware, the controller or model. The simplest way is to use the model callbacks such as `beforeValidate` or `beforeSave`.

```php
use Origin\Utility\Date;
$entity->created_date = Date::parseDatetime($entity->created_date); //31/01/2019 10:00 AM -> 2019-01-31 09:00:00
```