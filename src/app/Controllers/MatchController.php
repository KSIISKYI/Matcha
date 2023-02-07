<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Service\MatchService;

class MatchController extends Controller
{
    public function index(Request $request, Response $response)
    {   
        $view = Twig::fromRequest($request);

        return $view->render($response, 'find_match.twig');
    }

    public function checkForMatch(Request $request, Response $response, $args)
    {
        $data = $request->getParsedBody();
        MatchService::markViewed($this->container, $args['profile_id']);
        $response->withHeader('Content-Type', 'application/json');

        if ($data['is_like']) {
            MatchService::likeProfile($this->container, $args['profile_id']);
            $res = MatchService::checkForMacth($this->container, $args['profile_id']);
            $response->getBody()->write(json_encode($res));
        } else {
            $response->getBody()->write(json_encode([]));
        }

        return $response;
    }
}
