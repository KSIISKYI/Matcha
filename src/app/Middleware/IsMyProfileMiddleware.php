<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

use App\Models\Profile;

class IsMyProfileMiddleware extends Middleware
{
    public function __invoke(Request $request, RequestHandler $handler) {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();
        $profile = Profile::find($args['profile_id']);

        $response = new Response();

        if (!$profile) {
            return $response->withStatus(404, 'Not Found');
        } else if ($profile->user->id === $this->container->get('user')->id) {
            return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('profile-index'));
        } else {
            $response = $handler->handle($request);
            return $response;
        }
    }
}
