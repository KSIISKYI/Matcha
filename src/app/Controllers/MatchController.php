<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Service\{MatchService, Paginator, ProfileService};

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

    public function getMyMatches(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $view = Twig::fromRequest($request);
    
        $paginator = new Paginator(
            ProfileService::getMatchProfiles($this->container),
            4
        );

        $context = [
            'profiles' => $paginator->getData(isset($data['page']) ? $data['page'] : 1),
            'page_obj' => $paginator->getPageObj(isset($data['page']) ? $data['page'] : 1)
        ];

        return $view->render($response, 'profile/profiles.twig', $context);
    }
}
