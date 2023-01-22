<?php

use DI\Container;

return function(Container $container) {
    $container->set('settings', function() {
        return [
            'name' => 'Matcha',
            'displayErrorDetails' => true,
            'logErrorDetails' => true,
            'logErrors' => false,

            'views' => [
                'template_path' => __DIR__ . '/../resources/views',
            ],
            'db' => [
                'driver' => 'mysql',
                'host' => '127.0.0.1',
                'database' => 'matcha',
                'username' => 'root',
                'password' => 'root',
                'charset' => 'utf8',
                'collation' => 'utf8_unicode_ci',
                'prefix' => '',
            ]
        ];
    });
};
