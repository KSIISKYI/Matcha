<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class FlashMessageMiddleware extends Middleware
{
    public function __invoke(Request $request, RequestHandler $handler) {
        $this->container->get('flash')->__construct($_SESSION);

        $response = $handler->handle($request);

        return $response;
    }
}
