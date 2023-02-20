<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{Profile, Chat, Message, Participant};
use App\Service\{Paginator, ProfileService};
use App\Service\Chat\ChatService;

class ChatController extends Controller
{
    public function index(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $view = Twig::fromRequest($request);
        $chats = ChatService::getChats($this->container);
        $paginator = new Paginator(
            $chats,
            5
        );

        $context = [
            'chats' => $paginator->getData(isset($data['page']) ? $data['page'] : 1),
            'page_obj' => $paginator->getPageObj(isset($data['page']) ? $data['page'] : 1)
        ];
        
        return $view->render($response, 'chat/index.twig', $context);
    }

    public function show(Request $request, Response $response, $args)
    {
        $view = Twig::fromRequest($request);
        $chat = Chat::find($args['chat_id']);
        $chat->touch();

        if (isset($request->getHeaders()['Content-Type']) && $request->getHeaders()['Content-Type'][0] === 'application/json') {
            $my_profile = $this->container->get('user')->profile;

            if ($chat->participants->first()->profile->id === $my_profile->id) {
                $other_participant = $chat->participants->last()->load('profile');
                $my_participant = $chat->participants->first()->load('profile');
            } else {
                $other_participant = $chat->participants->first()->load('profile');
                $my_participant = $chat->participants->last()->load('profile');
            }

            $data = [
                'my_participant' => $my_participant->toArray(),
                'other_participant' => $other_participant->toArray(),
                'chat' => $chat->toArray()
            ];

            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($data));

            return $response;
        }

        return $view->render($response, 'chat/show2.twig');
    }

    public function getMessages(Request $request, Response $response, $args)
    {
        $my_profile = $this->container->get('user')->profile;
        $data = $request->getQueryParams();
        $current_page_number = isset($data['page']) ? $data['page'] : 1;

        $messages = Chat::find($args['chat_id'])->messages()->orderBy('id', 'desc')->paginate(7, ['*'], 'page', $current_page_number);

        $messages->flatMap(function($el) use($my_profile) {
            if (!in_array($el->participant_id, $my_profile->participants->pluck('id')->toArray())) {
                $el->reviewed = true;
                $el->save();
            }
        });

        $data = $messages->withPath($this->container->get('router')->urlFor('messages-index', ['chat_id' => $args['chat_id']]))->toArray();

        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($data));

        return $response;
    }
}
