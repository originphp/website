---
title: Markdown Utility
description: Markdown Utility Guide for the OriginPHP Framework
extends: _layouts.documentation
section: content
---
# Markdown Utility

The Markdown utility gives you useful functions for converting to and from markdown.

## Installation

To install this package

```linux
$ composer require originphp/markdown
```

To use

```php
use Origin\Markdown\Markdown;
```

## Convert To Html

To convert HTML to markdown

```php
$html = Markdown::toHtml($markdown);
```

## Convert To Text

To convert markdown to text:

```php
$text = Markdown::toText($markdown);
```

## Convert From HTML

To convert HTML to markdown.

```php
$markdown = Markdown::fromHtml($html);
```