<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

class IsProfilePhotoCreatorMiddleware extends Middleware
{
    function __invoke(Request $request, RequestHandler $handler) {
        
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();
        $user = $this->container->get('user');

        if ($user->profile->profile_photos()->find($args['profile_image_id'])) {
            return $handler->handle($request);
        }

        $response = new Response();

        return $response->withStatus(403, 'Forbidden');
    }
}
