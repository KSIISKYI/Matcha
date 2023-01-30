<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

class IsNativeUserMiddleware extends Middleware
{
    function __invoke(Request $request, RequestHandler $handler) {
        if (!$this->container->get('user')->is_google_auth) {
            $response = $handler->handle($request);
            return $response;
        }

        $response = new Response();

        return $response->withStatus(403, 'Forbidden');
    }
}
