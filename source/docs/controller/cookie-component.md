---
title: Cookie Component
description: Cookie Component Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Cookie Component

You can work with cookies from controllers and views, The cookie component allows you easily work with cookies. All cookie values are stored as a string and by default they are automatically encrypted. Arrays are converted to json strings.

Here are some examples how to use it:

```php
class ContactsController extends ApplicationController
{
    public function createCookies()
    {
      $this->Cookie->write('for-a-month',rand());
      $this->Cookie->write('for-seven-days',rand(),'+7 days');
    }
    public function readCookie()
    {
        return $this->Cookie->read('monster');
    }
    public function deleteCookie()
    {
        if($this->Cookie->exists('monster')){
            $this->Cookie->delete('monster');
        }
        
    }
}
```

If you don't want the values of the cookies to be encrypted, then you can disable this when writing the cookie value.

```php
$this->Cookie->write('my_app','some_value','+6 months',[
    'encrypt' => false
    ]);
```

You can also delete all cookies using the `destroy` method.