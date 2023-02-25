<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;

use App\Models\{Gender, Interest, Profile};
use App\Service\{ProfileService, UserService, Paginator};

class ProfileController extends Controller
{
    public function showMe(Request $request, Response $response)
    {   
        $user = $this->container->get('user');
        
        if (isset($request->getHeaders()['Content-Type']) && $request->getHeaders()['Content-Type'][0] === 'application/json') {
            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($user->profile));

            return $response;
        } else {
            $view = Twig::fromRequest($request);
            $profile = ProfileService::getProfile($this->container, $user->profile->id);
            
            return $view->render($response, 'profile/profile.twig', ['profile' => $profile]);
        }
    }

    public function showSettings(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $user = $this->container->get('user');
        $context = $this->container->get('flash')->getMessages();

        $context = array_merge(
            [
                'user' => $user, 
                'profile_photos' => $user->profile->profile_photos->toArray(),
                'genders' => Gender::all(),
                'interests' => Interest::all(),
            ],
            $context
        );    
        
        return $view->render($response, 'profile/profile_settings.twig', $context);
    }

    public function update(Request $request, Response $response)
    {
        $data = $request->getParsedBody();
        $flash = $this->container->get('flash');
        $errors = ProfileService::validateProfileForm($this->container, $data);

        if ($errors) {
            $flash->addMessage('data', $data);
            $flash->addMessage('errors', $errors);
        } else {
            UserService::updateUser($this->container->get('user'), $data);
            ProfileService::updateProfile($this->container, $data);
        }

        return $response;
    }

    public function show(Request $request, Response $response, array $args)
    {
        ProfileService::addToActivityLog($this->container, $args['profile_id']);
        $profile = ProfileService::getProfile($this->container, $args['profile_id']);
        $view = Twig::fromRequest($request);
        
        if (isset($request->getHeaders()['Content-Type']) && $request->getHeaders()['Content-Type'][0] === 'application/json') {
            $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($profile));

            return $response;
        } else {
            return $view->render($response, 'profile/profile.twig', ['profile' => $profile]);
        }        
    }

    public function getProfiles(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $profiles = ProfileService::getSuitableProfiles($this->container);
        $paginator = new Paginator(
            $profiles,
            2
        );

        $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($paginator->getData(isset($data['page']) ? $data['page'] : 1)));

        return $response;
    }

    public function getActivityLog(Request $request, Response $response)
    {
        $data = $request->getQueryParams();
        $profiles = ProfileService::getActivityLog($this->container);
        $view = Twig::fromRequest($request);
    
        $paginator = new Paginator(
            $profiles,
            4
        );

        $context = [
            'profiles' => $paginator->getData(isset($data['page']) ? $data['page'] : 1),
            'page_obj' => $paginator->getPageObj(isset($data['page']) ? $data['page'] : 1)
        ];

        return $view->render($response, 'profile/profiles.twig', $context);
    }

    public function blockProfile(Request $request, Response $response, $args)
    {
        if (!ProfileService::blockProfile($this->container, $args)) {
            return $response->withStatus(400, 'Bad Request');
        }

        return $response;
    }

    public function unblockProfile(Request $request, Response $response, $args)
    {
        if (!ProfileService::unblockProfile($this->container, $args)) {
            return $response->withStatus(400, 'Bad Request');
        }

        return $response;
    }

    public function reportFakeProfile(Request $request, Response $response, $args)
    {
        if (!ProfileService::reportFakeProfile($this->container, $args)) {
            return $response->withStatus(400, 'Bad Request');
        }

        return $response;
    }
}
