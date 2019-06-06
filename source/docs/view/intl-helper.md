---
title: Int Helper
description: Int Helper
extends: _layouts.documentation
section: content
---
# Intl Helper

The IntlHelper uses PHP's intl extension to format dates and numbers. The date and number utilitites that it uses can also parse international dates and numbers but thats a minefield if you ask me - and if you want to use a datepicker...

You can configure the defaults in your `AppController` like this:

```php
use Origin\I18n\Number;
use Origin\I18n\Date;

public function initialize(){
    Number::locale('en_GB');
    Date::locale('en_GB');
}
```

> If you are using [internationalization](/docs/development/internationalization-i18n) then the Date and Number utilities will be configured automatically when you call **I18n::initialize()**.

## Number

Formats a number, this is used by all other functions in the backend.

```php
    echo $this->Intl->number(1234.56); // 1,234.56
```

You can also pass and array of options with the following keys:

- *precision* The maximum number of decimal places
- *places* - The minimum number of decimal places
- *before* - What to show before the string
- *after*- What to show after the string
- *pattern* - a [PHP intl](http://php.net/manual/en/class.numberformatter.php) extension pattern e.g #,##0.###

## Currency

Formats a number into a currency.

```php
    echo $this->Intl->currency(1234.56,'USD'); // $1,234.56
```

You can also pass and array of options with the following keys:
- *precision* The maximum number of decimal places
- *places* - The minimum number of decimal places
- *before* - What to show before the string
- *after*- What to show after the string
- *pattern* - a [PHP intl](http://php.net/manual/en/class.numberformatter.php) extension pattern e.g #,##0.###

## Decimal

Formats a number with a max number of decimal places.,

```php
    echo $this->Intl->decimal(1234.56,2; // 1,234.56
```

You can also pass and array of options with the following keys:
- *places* - The minimum number of decimal places
- *before* - What to show before the string
- *after*- What to show after the string
- *pattern* - a [PHP intl](http://php.net/manual/en/class.numberformatter.php) extension pattern e.g #,##0.###


## Date

```php
echo $this->Intl->date('2018-12-31'); // 31/12/2018
```

## Datetime

```php
echo $this->Intl->datetime('2018-12-31 19:21:00'); // 31/12/2018 19:21
```

## Time

```php
echo $this->Intl->time('19:21:00'); // 19:21
```

You can also pass different formats to the date functions either as string for a pattern or an array.

```php
echo $this->Intl->datetime('2018-12-31 19:21:00','dd MMM, y H:mm'); // Pattern
echo $this->Intl->datetime('2018-12-31 19:21:00',['IntlDateFormatter::NONE, IntlDateFormatter::FULL]); // Array with format options for date + time
```


## Parsing

The date formatter assumes that the dates that you are formatting are in MySQL format, e.g. Y-m-d H:i:s. You can use the Date utility to delocalize user submitted data to convert to this format and timezone.

This can be done in middleware, the controller or model. The simplest way is to use the model callbacks such as `beforeValidate` or `beforeSave`.

```php
use Origin\Utility\Date;
$entity->created_date = Date::parseDatetime($entity->created_date); //31/01/2019 10:00 AM -> 2019-01-31 09:00:00
```