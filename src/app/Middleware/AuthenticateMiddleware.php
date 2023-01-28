<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response;

use App\Models\User;

class AuthenticateMiddleware extends Middleware
{
    function __invoke(Request $request, RequestHandler $handler) {
        if (isset($_SESSION['user']) && $user = User::find($_SESSION['user'])) {
            $user->profile->last_activity = date("Y-m-d H:i:s", time());
            $user->profile->save();

            $this->container->set('user', function() use($user) {
                return $user;
            });

            $response = $handler->handle($request);
            
            return $response;
        }

        $response = new Response();

        return $response->withStatus(301)->withHeader('Location', $this->container->get('router')->urlFor('signin-get'));
    }
}
