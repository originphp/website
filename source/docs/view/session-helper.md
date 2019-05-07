---
title: Session Helper
description: Session Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Session Helper

The Session Helper works exactly the same way as the Session Component, the only difference is the Session Helper is available
in your views.

```php
$value =  $this->Session->read('monster');
$this->Session->write('forever');
$this->Session->delete('monster');
```

For more information see the [Session Component Guide](controller/Session-component).