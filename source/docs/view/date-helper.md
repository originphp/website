---
title: Date Helper
description: Date Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Date Helper

The date helper makes it easy to format dates, in your `ApplicationController` setup the default timezone, and date formats (using PHP date function style formats), and the date helper will automatically format dates and times unless you tell it to use a different format. The date helper uses the Date utility.

To localize your web application, call the initialize from your

```php
class ApplicationController extends Controller
{
    public function initialize() : void
    {
        I18n::initialize(['locale' => 'en_GB','language'=>'en','timezone'=>'Europe/London']);
    }
}
```

The locale configuration file can be found in `config/locales`, by default OriginPHP comes with two preconfigured locales. However, depending upon your needs you can either create a specific locale that you need or use the `locale::generate` command to do this for you, see [Internationalization I18N](/docs/development/internationalization-i18n) for more information on how to automatically generate locales files.

Or if you want to set the date settings manually.

```php
use Origin\Utility\Date;

public function initialize() : void
{
     Date::locale([
         'timezone' => 'UTC',
         'date' => 'm/d/Y',
         'datetime' => 'm/d/Y H:i',
         'time' => 'H:i'
         ]);
}
```

## Formating

From within your view you would use like this, it will automatically format the date/time/datetime depending upon the field type. The date helper assumes that the date in the database is stored in MySQL date/time formats.

Times will be converted if you the timezone differs (set with I18n) from the the PHP script (UTC). A date must be present in the datestring for the time to be converted due to daylight savings.

```php
echo $this->Date->format($article->created); // From 2019-01-01 13:45:00 to 01/01/2019 13:45
echo $this->Date->format($article->created,'H:i'); // From 2019-01-01 13:45:00 to 12:45 (time converted)
echo $this->Date->format($article->time,'H:i'); // From 13:45:00 to 13:45 (time NOT converted)
```

If you want to format it a different way, you can do so. Time values will still be converted to local time if you set the timezone to anything other than UTC.

```php
echo $this->Date->format($article->created,'F jS Y'); // January 1st 2019
```

## TimeAgoInWords

You can also format datetime strings such as x minutes/months/years ago etc

```php
echo $this->Date->timeAgoInWords($article->created); // 3 minutes ago
```

## Parsing

OriginPHP comes with the Delocalize Behavior, which automatically converts dates,numbers to and from the database. You can disable this in the `ApplicationModel` initialize method.

If you need to manually parse dates etc, use the Date utility. The date formatter assumes that the dates that you are formatting are in MySQL format, e.g. Y-m-d H:i:s. You can use the Date utility to delocalize user submitted data to convert to this format and timezone.

This can be done in middleware, the controller or model. The simplest way is to use the model callbacks such as `beforeValidate` or `beforeSave`.

```php
use Origin\Utility\Date;
$entity->created_date = Date::parseDatetime($entity->created_date); //31/01/2019 10:00 AM -> 2019-01-31 09:00:00
```