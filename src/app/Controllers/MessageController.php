<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{Chat, Message};
use App\Service\MessageService;

class MessageController extends Controller
{
    public function index(Request $request, Response $response, $args)
    {
        $data = $request->getQueryParams();
        $current_page_number = isset($data['page']) ? $data['page'] : 1;
        $data = MessageService::getMessages($this->container, $args, $current_page_number);

        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response;
    }

    // public function update(Request $request, Response $response, $args)
    // {
    //     $my_profile = $this->container->get('user')->profile;
    //     $data = $request->getParsedBody();
    //     $new_context = isset($data['new_context']) ? $data['new_context'] : '';
    //     MessageService::updateMessage($args['message_id'], $args['chat_id'], $new_context, $my_profile);

    //     return $response;
    // }

    // public function destroy(Request $request, Response $response, $args)
    // {
    //     $my_profile = $this->container->get('user')->profile;
    //     MessageService::removeMessage($args['message_id'], $args['chat_id'], $my_profile);

    //     return $response;
    // }
}
