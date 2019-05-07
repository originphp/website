---
title: Flash Component
description: Flash Component Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Flash Component

The Flash component enables you to display messages to the user either in the current request or on the next if redirecting.

```php
class ContactsController extends AppController
{
    public function edit($id){
        if($result){
            $this->Flash->success('Result is true');
        }
        else{
            $this->Flash->error('Result is false');
        }
    }
}
```

Each type of message will rendered in a div with its own class. The Flash component has the following methods:

- `info`
- `success`
- `warning`
- `error`
