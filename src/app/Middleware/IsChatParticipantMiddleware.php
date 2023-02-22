<?php

namespace App\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;
use Slim\Psr7\Response;

use App\Models\{Chat};

class IsChatParticipantMiddleware extends Middleware
{
    public function __invoke(Request $request, RequestHandler $handler) {
        $args = RouteContext::fromRequest($request)->getRoute()->getArguments();
        $my_profile = $this->container->get('user')->profile;
        $chat = Chat::find($args['chat_id']);
        $response = new Response();

        if ($chat && $chat->participants->pluck('id')->intersect($my_profile->participants->pluck('id'))->count()) {
            $response = $handler->handle($request);
            return $response;
        }

        return $response->withStatus(302)->withHeader('Location', $this->container->get('router')->urlFor('chats-index'));
    }
}
