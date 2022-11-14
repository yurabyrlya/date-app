<?php

declare(strict_types=1);

use DI\Container;
use Monolog\Logger;

return function (Container $container) {
    $container->set('settings', function() {
        return [
            'name' => 'Date App',
            'appKey' => 'secret',
            'displayErrorDetails' => true,
            'logErrorDetails' => true,
            'logErrors' => true,
            'logger' => [
                'name' => 'slim-app',
                'path' => __DIR__ . '/../logs/app.log',
                'level' => Logger::DEBUG,
            ],
            'views' => [
                'path' => __DIR__ . '/../resources/views',
                'settings' => ['cache' => false],
            ],
            'connection' => [
                'driver' => 'mysql',
                'host' => 'date-app_db_1',
                'database' => 'db',
                'username' => 'user',
                'password' => 'secret',
                'collation' => 'utf8_general_ci',
                'prefix' => ''
            ]
        ];
    });
};
