---
title: Cookie Helper
description: Cookie Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Cookie Helper

The Cookie helper works exactly the same way as the Cookie Component, the only difference is the Cookie Helper is available in your views.

```php
$value =  $this->Cookie->read('monster');
$this->Cookie->write('foo',rand());
$this->Cookie->delete('monster');
```

For more information see the [Cookie Component Guide](/docs/controller/cookie-component).