---
title: Html Helper
description: Html Helper Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Html Helper

The Html helper provides some useful tools for working with html.

## Links

To create a generate a link for  `/articles/view/1234` you can pass an array. You don't need to pass a controller name
if the link is to the same controller.

```php
echo $this->Html->link('click me', ['controller' => 'Articles','action' => 'view',1234]);
```

When using array you can also pass the following additional keys.

- `?`: this should be array of key value pairs to generate a query string after the url
- `#`: for html fragements, this a link on the page which will cause it to scroll

All keys that are an integer will be passed as an argument. So to get `/articles/view/1234/abc`, use the following array.

```php
echo $this->Html->link('click me',['controller' => 'Articles','action' => 'view',1234,'abc']);
```

To work with an extension which is added to the url e.g. `https://locallost/articles/view/1234.json`, remember
to make sure in the Route for this you add the `ext` key.

```php
echo $this->Html->link('view', ['controller' => 'Articles','action' => 'view',1234 ,'ext' => 'json']);
```

You can also just pass a string

```php
echo $this->Html->link('click me','/articles/action/1234');
```

## Scripts Tags

To load a javascript file from the `public/js` folder, this example we want to load `form.js`.

```php
echo $this->Html->js('form');
```

If you want to load from a different folder, make sure you start the name with a forward slash.

```php
echo $this->Html->js('/assets/js/form.js');
```

You can also load external files

```php
echo $this->Html->js('https://code.jquery.com/jquery-3.3.1.min.js');
```

You can also load a script inline  

```php
echo $this->Html->js('form',['inline'=>true]);
```

You can also load a script from a plugin public folder, this will automatically load the contents inline of the view. This should only be used for development purposes for performance reasons. Once in production move the file or create a symlink to it.

```php
echo $this->Html->js('Myplugin.custom.js');
```

## Stylesheets

Similar to loading scripts, you can use the css method.

```php
echo $this->Html->css('bootstrap');
```

To load from a different folder.

```php
echo $this->Html->css('/assets/css/bootstrap.css');
```

To load a stylesheet located on the web.

```php
echo $this->Html->css('https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css');
```

You can also load a stylesheet inline.

```php
echo $this->Html->css('bootstrap',['inline'=>true]);
```


You can also load a script from a plugin public folder, this will automatically load the contents inline of the view. This should only be used for development purposes for performance reasons. Once in production move the file or create a symlink to it.

```php
echo $this->Html->css('Myplugin.custom.css');
```


## Images

You can easily add images to your view, you must provide an extension and if the name does not start with a forward slash it will look for the image in the `public/img` folder.

```php
echo $this->Html->img('spinner.png');
echo $this->Html->img('/somewherelse/images/spinner.gif'); // from public folder
```

## Div

To wrap content in a DIV tag, and you can pass an array with optional attributes.

```php
echo $this->Html->div($content);
echo $this->Html->div($content,['class' => 'highlight']);
```