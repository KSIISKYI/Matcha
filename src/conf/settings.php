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
                'driver' => $_ENV['DRIVER'],
                'host' => $_ENV['HOST'],
                'database' => $_ENV['DATABASE'],
                'username' => $_ENV['USERNAME'],
                'password' => $_ENV['PASSWORD'],
                'charset' => $_ENV['CHARSET'],
                'collation' => $_ENV['COLLATION'],
                'prefix' => $_ENV['PREFIX'],
            ]
        ];
    });
};
