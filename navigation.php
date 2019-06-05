<?php

return [
    'Getting Started' => [
        'url' => 'docs/getting-started',
    ],
    'Controllers' => [
        'url' => 'docs/controllers',
        'children' => [
            'Request Object' => 'docs/controller/request',
            'Response Object' => 'docs/controller/response',
            'Components' => 'docs/controller/components',
            'Session Component' => 'docs/controller/session-component',
            'Cookie Component' => 'docs/controller/cookie-component',
            'Flash Component' => 'docs/controller/flash-component',
            'Auth Component' => 'docs/controller/auth-component',
        ],
    ],
    'Models' => [
        'url' => 'docs/models',
        'children' => [
            'Associations' => 'docs/model/associations',
            'Entities' => 'docs/model/entities',
            'Finding Records' => 'docs/model/finding-records',
            'Validation' => 'docs/model/validation',
            'Callbacks' => 'docs/model/callbacks',
            'Behaviors' => 'docs/model/behaviors',
        ],
    ],
    'Views' => [
        'url' => 'docs/views',
        'children' => [
            'Helpers' => 'docs/view/helpers',
            'Html Helper' => 'docs/view/html-helper',
            'Form Helper' => 'docs/view/form-helper',
            'Number Helper' => 'docs/view/number-helper',
            'Date Helper' => 'docs/view/date-helper',
            'Cookie Helper' => 'docs/view/cookie-helper',
            'Session Helper' => 'docs/view/session-helper',
            'Intl Helper' => '/docs/view/intl-helper'
        ],
    ],
    'Console Commands' => [
        'url' => 'docs/console-commands',
        'children' => [
           // 'Tasks' => 'docs/console/tasks'
        ],
    ],
    'Plugins' => [
        'url' => 'docs/plugins',
    ],
    'Development' => [
        'url' => 'docs/development',
        'children' => [
            'Routing' => 'docs/development/routing',
            'Configuration' => 'docs/development/configuration',
            'Middleware' => 'docs/development/middleware',
            'Code Generation' => 'docs/development/code-generation',
            'Events' => 'docs/development/event-manager',
            'Logging' => 'docs/development/logging',
            'Debugging' => 'docs/development/debugging',
            'Testing' => 'docs/development/testing',
            'Migrations' => 'docs/development/migrations',
            'Internationalization (I18n)' => 'docs/development/internationalization-i18n',
        ],
    ],
    'Utilities' => [
        'url' => 'docs/utilities',
        'children' => [
            'Cache' => 'docs/utility/cache',
            'Collection' => 'docs/utility/collection',
            'Dom' => 'docs/utility/dom',
            'Email' => 'docs/utility/email',
            'Queue' => 'docs/utility/queue',
            'Storage' => 'docs/utility/storage',
            'XML' => 'docs/utility/xml',
            'Yaml' => 'docs/utility/yaml',
        ],
    ],
];
