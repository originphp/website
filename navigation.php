<?php

return [
    'Getting Started' => [
        'url' => 'docs/getting-started',
    ],
    'Tutorial' => [
        'url' => 'docs/tutorial',
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
            'Migrations' => 'docs/development/migrations',
            'Testing' => 'docs/development/testing',
            'Events' => 'docs/development/event-manager',
            'Debugging' => 'docs/development/debugging',
            'Logging' => 'docs/development/logging',
            'Internationalization (I18n)' => 'docs/development/internationalization-i18n',
        ],
    ],
    'Utilities' => [
        'url' => 'docs/utilities',
        'children' => [
            'Cache' => 'docs/utility/cache',
            'Collection' => 'docs/utility/collection',
            'CSV' => 'docs/utility/csv',
            'Dom' => 'docs/utility/dom',
            'Email' => 'docs/utility/email',
            'File' => 'docs/utility/file',
            'Folder' => 'docs/utility/folder',
            'Queue' => 'docs/utility/queue',
            'Storage' => 'docs/utility/storage',
            'XML' => 'docs/utility/xml',
            'Yaml' => 'docs/utility/yaml',
        ],
    ],
];
