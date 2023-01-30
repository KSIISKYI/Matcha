<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use App\Models\{Gender, Interest, User, PendingUser};
use App\Service\{ProfileService, UserService};

class ProfileController extends Controller
{
    function show(Request $request, Response $response)
    {   
        $view = Twig::fromRequest($request);
        $user = $this->container->get('user');
        $context = ['profile_photos' => $user->profile->profile_photos, 'user' => $user];
        
        return $view->render($response, 'profile/profile.twig', $context);
    }

    function showSettings(Request $request, Response $response)
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

    function update(Request $request, Response $response)
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
}
