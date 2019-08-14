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
            'Routing' => 'docs/controller/routing',
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
    'Middleware' => [
        'url' => 'docs/middleware',
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
    'Libraries' => [
        'url' => 'docs/libraries',
        'children' => [
            'Cache' => 'docs/cache',
            'Events' => 'docs/event-manager',
            'Logging' => 'docs/log',
            'Migrations' => 'docs/migrations',
            'Queue' => 'docs/queue',
            'Storage' => 'docs/storage',
            'ElasticSearch' => 'docs/elasticsearch',
        ]
        ],
    'Development' => [
        'url' => 'docs/development',
        'children' => [
            'Configuration' => 'docs/development/configuration',
            'Code Generation' => 'docs/development/code-generation',
            'Debugging' => 'docs/development/debugging',
            'Docker (DDE)' => 'docs/development/dockerized-development-environment',
            'Internationalization (I18n)' => 'docs/development/internationalization-i18n',
            'Testing' => 'docs/development/testing'
        ],
    ],
    'Utilities' => [
        'url' => 'docs/utilities',
        'children' => [
            'Collection' => 'docs/utility/collection',
            'CSV' => 'docs/utility/csv',
            'Dom' => 'docs/utility/dom',
            'Email' => 'docs/utility/email',
            'File' => 'docs/utility/file',
            'Folder' => 'docs/utility/folder',
            'Html' => 'docs/utility/html',
            'Http' => 'docs/utility/http',
            'Markdown' => 'docs/utility/markdown',
            'Security' => 'docs/utility/security',
            'Text' => 'docs/utility/text',
            'XML' => 'docs/utility/xml',
            'Yaml' => 'docs/utility/yaml'
        ],
    ],
];
