---
title: Internationalization (I18n)
description: Internationalization & Localization (I18n)
extends: _layouts.documentation
section: content
---
# Internationalization (I18n)

Inernationalization of your app includes translating messages and displaying dates and times in the correct formats and timezones as well as formating numbers properly in multiple languages.

To localize your web application, call the initialize from your

```php
class AppController extends Controller
{
    public function initialize(){
        parent::initialize();
        I18n::initialize(['locale' => 'en_GB','language'=>'en','timezone'=>'Europe/London']);
    }
}
```

The initialize method will locale definitions from the `config/locales` folder if available and then configure the Date and Number utilities accordingly. You can run the `locale:generate` command to generate locales files.

Locale generation requires the PHP Intl extension (This is installed by default in the Dockerized Development Environment). However, if you need to install this on your server use the following command :

```linux
$ apt-get install php-intl
```

To generate specific locales, separate each locale by space

```linux
$ bin/console locale:generate en_UK en_US
```

To generate all the possible locales, approx 720, don't pass any arguments.

```linux
$ bin/console locale:generate
```

Whenever you use the [NumberHelper](/docs/view/number-helper) or [DateHelper](/docs/view/date-helper) data will be localized automatically. Note. 

## Translations

Translations are stored in the `app/Locale` folder, and the filename should be the language code with the `php` extension. E.g. `es.php` for the Spanish language. When I18n is initialized it sets the locale and language for locale, unless you specify something else. Then the translations are loaded if the translation file is found.

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

OriginPHP can parse users input (dates,numbers,time) using its own parsing functions or the PHP Intl extension. The framework also comes with the `DelocalizeBehavior` which uses the `Utility\Date` and `Utility\Number` classes to parse dates and numbers automatically.

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