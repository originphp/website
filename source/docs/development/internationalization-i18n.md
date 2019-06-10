---
title: Internationalization (I18n)
description: Internationalization & Localization (I18n)
extends: _layouts.documentation
section: content
---
# Internationalization (I18n)

Inernationalization of your app includes translating messages and displaying dates and times in the correct formats and timezones as well as formating numbers properly in multiple languages.

To setup internationalization you need to initialize I18n, so you would do this from your `Controller`, this will detect the locale from the user. Note. It does not detect the user timezone.

```php
use Origin\I18n\I18n;
class AppController extends Controller
{
    public function initialize(){
        I18n::initialize();
    }
}
```

To manually set this

```php
class AppController extends Controller
{
    public function initialize(){
        I18n::initialize(['locale' => 'en_GB','language'=>'en','timezone'=>'Europe/London']);
    }
}
```

The initialize method will locale definitions from the `config/locales` folder if available and then configure the Date and Number utilities accordingly. You can run the `locales:generate` command to generate locales files.

To generate specific locales, separate each locale by space

```linux
$ bin/console locales:generate en_UK en_US
```

To generate all the possible locales, approx 720, don't pass any arguments.

```linux
$ bin/console locales:generate
```

Whenever you use the [NumberHelper](/docs/view/number-helper) or [DateHelper](/docs/view/date-helper) data will be localized automatically. Note. The date formating utilities assume thats dates are in UTC, and it will then convert from UTC to the configured timezone.

> If you prefer to use PHP Intl extension for your formating, then then you don't need to use the `locales:generate` command see the [IntlHelper](/docs/view/intl-helper) for more information.

## Translations

Translations are stored in the `src/Locale` folder, and the filename should be the language code with the `php` extension. E.g. `es.php` for the Spanish language. When I18n is initialized it sets the locale and language for locale, unless you specify something else. Then the translations are loaded if the translation file is found.

The file itself should return an array

```php
return [
    'hello world' => 'Hola Mundo',
    'Hello {name}' => 'Hola {name}',
    'There is one apple|There are {count} apples' => 'Hay una manzana|Hay {count} manzanas'
]
```

To translate strings you use the `__` function. If a translation is found, it will use this.

```php
echo __('Hello world'); // Hola Mundo
```

To pass variables

```php
echo __('Hello {name}',['name'=>'Jon Snow']); // Hola Jon Snow
```

Pluralization is an important issue when translating into different languages. In English there is singular and plural (e.g. 1 apple, 2 apples), and in other languages such as Arabic or Russian there are many more.

To use singular and plurals, use the `|` between a string and use the key `count`.

```php
echo __('There is one apple|There are {count} apples',['count'=>2]); // Hay 2 manzanas
```

If you need to use a different string when the count is 0. Then do so like this:

```php
echo __('There are {count} apples|There is {count} apple|There are {count} apples',['count'=>1]); // Hay una manzana
```

By default, if the string for a zero count is not available it will use the other ie. for many.

## Parsing User Input

### Parsing Numbers

If you are using the normal Number utility & helper

```php
use Origin\Utility\Number;
$user->balance = Number::parse($user->balance); //1.000,23 -> 1000.23
```

If you wish to use PHP Intl extension

```php
use Origin\I18n\Number;
$user->balance = Number::parse($user->balance); //1.000,23 -> 1000.23
```

### Parsing Dates and Times

If you are using the normal Date utility and helper

```php
use Origin\Utility\Date;
$user->date = Date::parseDate($user->date); // 31/01/2019 -> 2019-01-01
$user->datetime = Date::parseDate($user->datetime); // 31/01/2019 10:50 -> 2019-01-01 09:50:00
$user->time = Date::parseTime($user->datetime); // 10:50 -> 09:50:00
```

If you want to use the PHP Intl extension for parsing.

```php
use Origin\I18n\Date;
$user->date = Date::parseDate($user->date); // 31/01/2019 -> 2019-01-01
$user->datetime = Date::parseDate($user->datetime); // 31/01/2019 10:50 -> 2019-01-01 09:50:00
$user->time = Date::parseTime($user->datetime); // 10:50 -> 09:50:00
```