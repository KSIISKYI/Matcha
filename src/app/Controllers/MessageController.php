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
}
