<?php

namespace App\Controllers;

use Slim\Views\Twig;
use Slim\Psr7\Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Rakit\Validation\Validator;

use App\Models\{User, PendingUser};

class ProfileController extends Controller
{
    function showProfile(Request $request, Response $response)
    {   
        $view = Twig::fromRequest($request);
        $user = $this->container->get('user');
        $context = ['profile_photos' => $user->profile->profile_photos, 'user' => $user];
        
        return $view->render($response, 'profile/profile.twig', $context);
    }

    function showProfileSettings(Request $request, Response $response)
    {
        $view = Twig::fromRequest($request);
        $user = $this->container->get('user');
        $context = ['profile' => $user->prifile, 'profile_photos' => $user->profile->profile_photos->toArray()];
        
        return $view->render($response, 'profile/profile_settings.twig', $context);
    }
}
