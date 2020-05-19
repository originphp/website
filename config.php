<?php

use Illuminate\Support\Str;

return [
    'baseUrl' => 'https://www.originphp.com/',
    'production' => false,
    'siteName' => 'OriginPHP',
    'siteDescription' => 'OriginPHP is an open-source MVC framework that enables PHP developers to quickly build high performance and scalable web applications.',

    // Algolia DocSearch credentials
    'docsearchApiKey' => '85dd81c266dc348ab5b9203f8740bf14',
    'docsearchIndexName' => 'originphp',

    // navigation menu
    'navigation' => require_once('navigation.php'),

    // helpers
    'isActive' => function ($page, $path) {
        return Str::endsWith(trimPath($page->getPath()), trimPath($path));
    },
    'isActiveParent' => function ($page, $menuItem) {
        if (is_object($menuItem) && $menuItem->children) {
            return $menuItem->children->contains(function ($child) use ($page) {
                return trimPath($page->getPath()) == trimPath($child);
            });
        }
    },
    'url' => function ($page, $path) {
        return Str::startsWith($path, 'http') ? $path : '/' . trimPath($path);
    },
];
