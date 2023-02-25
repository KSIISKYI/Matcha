<?php

require __DIR__ . '/../../../vendor/autoload.php';

use Ratchet\Server\IoServer;
use Ratchet\Http\HttpServer;
use Ratchet\WebSocket\WsServer;

use App\Service\Chat\Chat;

$server = IoServer::factory(
        new HttpServer(
            new WsServer(
                new Chat()
            )
        ),
        8090 // when changing the port, also change it in the chat.js(168 line) and notification.js(22 line) files.
    );

$server->run();
