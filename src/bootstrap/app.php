<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Slim\Factory\AppFactory;
use DI\Container;
use Illuminate\Database\Capsule\Manager as DB;
use Slim\Views\TwigMiddleware;
use Slim\Flash\Messages;
use Slim\Csrf\Guard;

// Looing for .env at the root directory
$dotenv = \Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$container = new Container;
$settings = require_once __DIR__ . '/../conf/settings.php';
$settings($container);

// flash initialization
$container->set('flash', function () {
    $storage = [];
    return new Messages($storage);
});

$db_s = $container->get('settings')['db'];

// orm initialization
$db = new DB;
$db->addConnection($db_s);
$db->setAsGlobal();
$db->bootEloquent();

// view initialization
$views = require_once __DIR__ . '/../app/views.php';
$views($container);

// vatidation initialization
$validation = require_once __DIR__ . '/../app/Validation/validation.php';
$validation($container);

AppFactory::setContainer($container);
$app = AppFactory::create();
$app->addBodyParsingMiddleware();


// csrf initialization
$responseFactory = $app->getResponseFactory();

$container->set('csrf', function () use ($responseFactory) {
    return new Guard($responseFactory);
});


$app->add(TwigMiddleware::create($app, $container->get('twig')));

$container = $app->getContainer();
$conf = $container->get('settings');

//set router function
$container->set('router', function() use ($app) {
    return $app->getRouteCollector()->getRouteParser();
});

$app->add(\App\Middleware\FlashMessageMiddleware::class);
$app->addErrorMiddleware($conf['displayErrorDetails'], $conf['logErrorDetails'], $conf['logErrors']);

//set base path
$app->setBasePath('');

//Controllers initialization
$container->set('RegisterController', function (Container $container) {
    return new \App\Controllers\Auth\RegisterController($container);
});

$container->set('HomeController', function (Container $container) {
    return new \App\Controllers\HomeController($container);
});

$container->set('ProfileController', function (Container $container) {
    return new \App\Controllers\ProfileController($container);
});

$container->set('AuthController', function (Container $container) {
    return new \App\Controllers\Auth\AuthController($container);
});

$container->set('ProfilePhotoController', function (Container $container) {
    return new \App\Controllers\ProfilePhotoController($container);
});

$container->set('UserController', function (Container $container) {
    return new \App\Controllers\UserController($container);
});

$container->set('GoogleAuthController', function (Container $container) {
    return new \App\Controllers\GoogleAuthController($container);
});

$container->set('DiscoverySettingsController', function (Container $container) {
    return new \App\Controllers\DiscoverySettingsController($container);
});

$container->set('MatchController', function (Container $container) {
    return new \App\Controllers\MatchController($container);
});

$container->set('ChatController', function (Container $container) {
    return new \App\Controllers\ChatController($container);
});

$routes = require_once __DIR__ . '/../app/routes.php';
$routes($app);
