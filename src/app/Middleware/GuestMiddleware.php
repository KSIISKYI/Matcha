<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

use App\Models\User;

class GuestMiddleware extends Middleware
{
    public function __invoke(Request $request, RequestHandler $handler) {
        $response = $handler->handle($request);

        if (isset($_SESSION['user']) && User::find($_SESSION['user'])) {
            return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('profile-index'));
        }
        return $response;
    }
}
