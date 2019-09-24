---
title: Session Component
description: Session Component Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Session Component

When you need to persist small amounts of data between requests you typically would use sessions. To access Session data from the controller you would use the Session component.

Session data is stored using key value pairs, you can also use dot notation to deeper levels of an array. For example, `userProfile.id` would first look for the key would look for the key `userProfile` and if its value is an array and has the key `id` it will return the value. If it there is no key set then it will return a `null` value.

```php
class ContactsController extends ApplicationController {
  public function getUserId(){
     return $this->Session->read('user_id');
  }
}
```

To store data in the session:

```php
class ContactsController extends ApplicationController {
  public function setUserId($id){
     $this->Session->write('user_id',$id);
  }
}
```

To delete an item from the session

```php
class ContactsController extends ApplicationController {
  public function deleteUserId(){
     $this->Session->delete('user_id');
  }
}
```

To check the Session has a particular key

```php
$result = $this->Session->exists('user.id');
```

To clear the session

```php
$this->Session->clear();
```

To destroy the session

```php
$this->Session->destroy();
```
